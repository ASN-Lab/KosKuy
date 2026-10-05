<?php

namespace App\Http\Controllers;

use App\Models\KosFacility;
use App\Models\KosListing;
use Illuminate\Http\Request;

class KosController extends Controller
{
    private const SORTS = [
        'terbaru' => 'terbaru',
        'termurah' => 'termurah',
        'termahal' => 'termahal',
        'nama' => 'nama',
    ];

    public function index(Request $request)
    {
        $filters = $this->filters($request);

        $query = KosListing::where('is_available', true)->with(['images', 'facilities', 'owner']);

        $this->applyFilters($query, $filters);

        match ($filters['sort']) {
            'termurah' => $query->orderBy('price_per_month')->orderByDesc('id'),
            'termahal' => $query->orderByDesc('price_per_month')->orderByDesc('id'),
            'nama' => $query->orderBy('title')->orderByDesc('id'),
            default => $query->latest(),
        };

        $kosListings = $query->paginate(12)->withQueryString();

        $activeFilters = $this->activeFilters($filters);

        return view('kos.index', [
            'kosListings' => $kosListings,
            'genderCounts' => $this->genderCounts($filters),
            'facilities' => $this->facilityOptions(),
            'activeFilters' => $activeFilters,
            'activeFilterCount' => $activeFilters->count(),
        ]);
    }

    public function show($id)
    {
        $kos = KosListing::with(['images', 'facilities', 'owner'])
            ->where('is_available', true)
            ->findOrFail($id);

        $similarKos = KosListing::where('is_available', true)
            ->where('id', '!=', $kos->id)
            ->where('gender_type', $kos->gender_type)
            ->with(['images', 'facilities', 'owner'])
            ->orderByRaw(
                'ABS(price_per_month - ?) ASC',
                [$kos->price_per_month]
            )
            ->take(3)
            ->get();

        // Fall back to the newest listings when nothing matches the same gender.
        if ($similarKos->isEmpty()) {
            $similarKos = KosListing::where('is_available', true)
                ->where('id', '!=', $kos->id)
                ->with(['images', 'facilities', 'owner'])
                ->latest()
                ->take(3)
                ->get();
        }

        return view('kos.show', compact('kos', 'similarKos'));
    }

    /**
     * Normalise and whitelist every supported query parameter.
     *
     * @return array{search: ?string, gender: ?string, min: ?int, max: ?int, facilities: array<int, string>, sort: string}
     */
    private function filters(Request $request): array
    {
        $gender = $request->query('gender_type');
        $sort = $request->query('sort');

        return [
            'search' => $request->filled('search')
                ? trim((string) $request->query('search'))
                : null,
            'gender' => in_array($gender, ['putra', 'putri', 'campur'], true) ? $gender : null,
            'min' => $request->filled('min_price') ? max(0, (int) $request->query('min_price')) : null,
            'max' => $request->filled('max_price') ? max(0, (int) $request->query('max_price')) : null,
            'facilities' => array_values(array_filter(
                (array) $request->query('facilities', []),
                static fn ($value) => is_string($value) && $value !== ''
            )),
            'sort' => isset(self::SORTS[$sort]) ? $sort : 'terbaru',
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function applyFilters($query, array $filters): void
    {
        if ($filters['search']) {
            $term = '%' . $filters['search'] . '%';

            $query->where(function ($inner) use ($term) {
                $inner->where('title', 'like', $term)
                    ->orWhere('address', 'like', $term)
                    ->orWhere('description', 'like', $term);
            });
        }

        if ($filters['gender']) {
            $query->where('gender_type', $filters['gender']);
        }

        if ($filters['min'] !== null) {
            $query->where('price_per_month', '>=', $filters['min']);
        }

        if ($filters['max'] !== null) {
            $query->where('price_per_month', '<=', $filters['max']);
        }

        foreach ($filters['facilities'] as $facility) {
            $query->whereHas(
                'facilities',
                fn ($inner) => $inner->where('facility_name', $facility)
            );
        }
    }

    /**
     * Sidebar counts for each gender option. Counts honour every active filter
     * except gender itself, so the numbers never contradict the result list.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, int>
     */
    private function genderCounts(array $filters): array
    {
        $counts = ['all' => 0];

        foreach (['putra', 'putri', 'campur'] as $gender) {
            $query = KosListing::where('is_available', true);

            $scoped = array_merge($filters, ['gender' => null]);
            $this->applyFilters($query, $scoped);

            $counts[$gender] = $query->where('gender_type', $gender)->count();
            $counts['all'] += $counts[$gender];
        }

        return $counts;
    }

    /**
     * Distinct facility names ordered by how often they appear.
     *
     * @return \Illuminate\Support\Collection<int, object>
     */
    private function facilityOptions()
    {
        return KosFacility::query()
            ->select('facility_name')
            ->selectRaw('COUNT(*) as listings_count')
            ->groupBy('facility_name')
            ->orderByDesc('listings_count')
            ->orderBy('facility_name')
            ->get();
    }

    /**
     * Build removable chips for everything currently applied.
     *
     * @param  array<string, mixed>  $filters
     * @return \Illuminate\Support\Collection<int, array{label: string, url: string}>
     */
    private function activeFilters(array $filters)
    {
        $chips = collect();

        if ($filters['search']) {
            $chips->push([
                'label' => 'Kata kunci: "' . $filters['search'] . '"',
                'url' => route('kos.index', request()->except(['search', 'page'])),
            ]);
        }

        if ($filters['gender']) {
            $chips->push([
                'label' => genderLabel($filters['gender']),
                'url' => route('kos.index', request()->except(['gender_type', 'page'])),
            ]);
        }

        if ($filters['min'] !== null || $filters['max'] !== null) {
            $label = $filters['min'] !== null && $filters['max'] !== null
                ? rupiah($filters['min']) . ' - ' . rupiah($filters['max'])
                : rupiah($filters['min'] ?? $filters['max']);

            $chips->push([
                'label' => $label,
                'url' => route('kos.index', request()->except(['min_price', 'max_price', 'page'])),
            ]);
        }

        foreach ($filters['facilities'] as $facility) {
            $remaining = array_values(array_diff($filters['facilities'], [$facility]));

            $query = request()->except(['facilities', 'page']);

            $chips->push([
                'label' => $facility,
                'url' => route('kos.index', $remaining ? array_merge($query, ['facilities' => $remaining]) : $query),
            ]);
        }

        return $chips;
    }
}