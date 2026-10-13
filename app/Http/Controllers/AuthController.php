<?php

namespace App\Http\Controllers;

use App\Models\KosListing;
use App\Models\KosRoom;
use App\Models\Payment;
use App\Models\Tenant;
use App\Models\ThriftProduct;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Email atau password tidak cocok dengan data kami.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:25', 'regex:/^[0-9+\-\s()]+$/'],
            'role' => ['required', 'in:student,owner'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'terms' => ['accepted'],
        ], [
            'terms.accepted' => 'Kamu harus menyetujui syarat dan ketentuan terlebih dahulu.',
            'phone.regex' => 'Nomor WhatsApp hanya boleh berisi angka, spasi, dan tanda +.',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'role' => $data['role'],
            'password' => $data['password'],
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Selamat bergabung, ' . $user->name . '! Akunmu sudah siap dipakai.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Kamu sudah keluar. Sampai jumpa lagi.');
    }

    public function dashboard(): View|RedirectResponse
    {
        $user = Auth::user();

        // Each role gets its own area. Admin runs the Filament panel; owners
        // manage their own listings; students get the browse-first dashboard.
        // Nothing is shared, so a student never sees owner/admin tooling.
        return match ($user->role) {
            'admin' => redirect()->route('filament.admin.pages.dashboard'),
            'owner' => $this->ownerDashboard($user),
            default => $this->studentDashboard(),
        };
    }

    /**
     * Listing manager dashboard: the kos and thrift rows this owner controls.
     */
    private function ownerDashboard(User $user): View
    {
        $kosListings = KosListing::where('owner_id', $user->id)
            ->with(['images', 'facilities'])
            ->latest()
            ->get();

        $thriftProducts = ThriftProduct::where('seller_id', $user->id)
            ->with(['images', 'category'])
            ->latest()
            ->get();

        $stats = [
            'kos' => $kosListings->count(),
            'kos_available' => $kosListings->where('is_available', true)->count(),
            'thrift' => $thriftProducts->count(),
            'thrift_active' => $thriftProducts->where('is_sold', false)->count(),
        ];

        return view('dashboard.owner', [
            'kosListings' => $kosListings,
            'thriftProducts' => $thriftProducts,
            'stats' => $stats,
            'rental' => $this->rentalOverview($kosListings->pluck('id')),
        ]);
    }

    /**
     * Occupancy + cash overview for the owner's own kos.
     *
     * @param  \Illuminate\Support\Collection<int, int>  $kosIds
     * @return array<string, mixed>
     */
    private function rentalOverview($kosIds): array
    {
        $period = now()->format('Y-m');

        $rooms = KosRoom::whereIn('kos_listing_id', $kosIds)
            ->with('activeTenant:id,name,kos_room_id')
            ->get();

        $activeTenants = Tenant::whereIn('kos_listing_id', $kosIds)
            ->where('status', 'active')
            ->with(['room:id,room_number', 'kosListing:id,title'])
            ->get();

        $monthPayments = Payment::whereIn('kos_listing_id', $kosIds)
            ->where('period', $period)
            ->get();

        $paidByTenant = $monthPayments->where('status', 'paid')->keyBy('tenant_id');
        $outstandingTenants = $activeTenants
            ->reject(fn (Tenant $tenant) => $paidByTenant->has($tenant->id))
            ->values();

        $roomsTotal = $rooms->count();
        $roomsOccupied = $rooms->whereNotNull('active_tenant')->count();

        $roomSummaries = $rooms
            ->groupBy('kos_listing_id')
            ->map(fn ($group) => [
                'total' => $group->count(),
                'occupied' => $group->whereNotNull('active_tenant')->count(),
            ]);

        return [
            'period' => $period,
            'period_label' => now()->translatedFormat('F Y'),
            'rooms_total' => $roomsTotal,
            'rooms_occupied' => $roomsOccupied,
            'rooms_vacant' => $roomsTotal - $roomsOccupied,
            'occupancy_rate' => $roomsTotal > 0 ? (int) round($roomsOccupied / $roomsTotal * 100) : 0,
            'tenants_active' => $activeTenants->count(),
            'expected_monthly' => (float) $activeTenants->sum('monthly_price'),
            'collected_monthly' => (float) $monthPayments->where('status', 'paid')->sum('amount'),
            'outstanding_amount' => (float) $outstandingTenants->sum('monthly_price'),
            'outstanding_tenants' => $outstandingTenants,
            'room_summaries' => $roomSummaries,
            'recent_payments' => Payment::whereIn('kos_listing_id', $kosIds)
                ->with(['tenant:id,name', 'kosListing:id,title'])
                ->latest()
                ->take(6)
                ->get(),
        ];
    }

    /**
     * Student dashboard: recommendations only. No listing management, no panel
     * access — students browse the storefront and reach owners over WhatsApp.
     */
    private function studentDashboard(): View
    {
        $kosListings = KosListing::where('is_available', true)
            ->with(['images', 'facilities', 'owner'])
            ->latest()
            ->take(6)
            ->get();

        $thriftProducts = ThriftProduct::where('is_sold', false)
            ->with(['images', 'category', 'seller'])
            ->latest()
            ->take(8)
            ->get();

        return view('dashboard.student', compact('kosListings', 'thriftProducts'));
    }
}