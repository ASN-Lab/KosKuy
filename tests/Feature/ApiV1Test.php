<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\KosListing;
use App\Models\ThriftProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Locks the mobile API contract (routes/api.php v1):
 * auth token, me, bookings, thrift purchases, notifications,
 * thrift upload, and server-side sort. All additive; the
 * session/Blade flows are covered elsewhere.
 */
class ApiV1Test extends TestCase
{
    use RefreshDatabase;

    private function authHeader(User $user): array
    {
        // Guard Sanctum di-cache per aplikasi; lupakan agar token
        // berikutnya (atau tanpa token) dievaluasi ulang.
        $this->app['auth']->forgetGuards();
        $token = $user->createToken('mobile')->plainTextToken;

        return ['Authorization' => "Bearer $token", 'Accept' => 'application/json'];
    }

    public function test_register_returns_token_and_student_role(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'Mahasiswa Baru',
            'email' => 'baru@example.test',
            'password' => 'rahasia-kuat',
            'phone' => '081234567890',
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.role', 'student')
            ->assertJsonStructure(['data' => ['token', 'user' => ['id', 'name', 'email', 'role']]]);

        $this->assertDatabaseHas('users', ['email' => 'baru@example.test']);
    }

    public function test_register_rejects_short_password(): void
    {
        $this->postJson('/api/v1/register', [
            'name' => 'X',
            'email' => 'x@example.test',
            'password' => 'pendek',
        ])->assertStatus(422)->assertJsonPath('success', false);
    }

    public function test_login_returns_token_and_rejects_wrong_password(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['token', 'user' => ['role']]]);

        $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'salah-sekali',
        ])->assertUnauthorized()->assertJsonPath('success', false);
    }

    public function test_me_show_update_and_logout(): void
    {
        $user = User::factory()->create();

        $this->getJson('/api/v1/me', $this->authHeader($user))
            ->assertOk()
            ->assertJsonPath('data.email', $user->email);

        $this->putJson('/api/v1/me', [
            'name' => 'Nama Baru',
            'phone' => '081000000001',
        ], $this->authHeader($user))->assertOk();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Nama Baru']);

        $this->postJson('/api/v1/logout', [], $this->authHeader($user))->assertOk();

        // Token sudah dicabut: tanpa header harus 401.
        $this->app['auth']->forgetGuards();
        $this->getJson('/api/v1/me')->assertUnauthorized();
    }

    public function test_booking_full_flow(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $student = User::factory()->create(['role' => 'student']);
        $kos = KosListing::factory()->for($owner, 'owner')->create();

        $store = $this->postJson('/api/v1/bookings', [
            'kos_id' => $kos->id,
            'booking_date' => now()->addDay()->toDateString(),
            'notes' => 'Butuh parkir motor',
        ], $this->authHeader($student));

        $store->assertCreated()->assertJsonPath('success', true);
        $bookingId = $store->json('data.id');

        $this->assertDatabaseHas('bookings', ['id' => $bookingId, 'status' => 'pending']);
        $this->assertDatabaseHas('app_notifications', ['user_id' => $owner->id, 'type' => 'booking']);

        $this->getJson('/api/v1/my-bookings', $this->authHeader($student))
            ->assertOk()
            ->assertJsonPath('data.0.kos.title', $kos->title);

        $this->postJson("/api/v1/bookings/$bookingId/cancel", [], $this->authHeader($student))
            ->assertOk();

        $this->assertDatabaseHas('bookings', ['id' => $bookingId, 'status' => 'canceled']);

        // Batal kedua kali ditolak.
        $this->postJson("/api/v1/bookings/$bookingId/cancel", [], $this->authHeader($student))
            ->assertStatus(422);
    }

    public function test_booking_unavailable_kos_is_rejected(): void
    {
        $student = User::factory()->create();
        $kos = KosListing::factory()->unavailable()->create();

        $this->postJson('/api/v1/bookings', [
            'kos_id' => $kos->id,
            'booking_date' => now()->addDay()->toDateString(),
        ], $this->authHeader($student))->assertStatus(422);
    }

    public function test_thrift_purchase_flow_and_guards(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $product = ThriftProduct::factory()->for($seller, 'seller')->create();

        $this->postJson("/api/v1/thrift/{$product->id}/purchase", [], $this->authHeader($buyer))
            ->assertOk()->assertJsonPath('success', true);

        $this->assertDatabaseHas('thrift_purchases', [
            'buyer_id' => $buyer->id,
            'thrift_product_id' => $product->id,
        ]);
        $this->assertDatabaseHas('app_notifications', ['user_id' => $seller->id, 'type' => 'purchase']);

        // Beli barang yang sudah terjual → 404 seperti show.
        $other = User::factory()->create();
        $this->postJson("/api/v1/thrift/{$product->id}/purchase", [], $this->authHeader($other))
            ->assertNotFound();

        // Beli barang sendiri ditolak.
        $mine = ThriftProduct::factory()->for($buyer, 'seller')->create();
        $this->postJson("/api/v1/thrift/{$mine->id}/purchase", [], $this->authHeader($buyer))
            ->assertStatus(422);

        $this->getJson('/api/v1/my-purchases', $this->authHeader($buyer))
            ->assertOk()
            ->assertJsonPath('data.0.product.title', $product->title);
    }

    public function test_notifications_index_and_mark_read(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $this->getJson('/api/v1/notifications', $this->authHeader($user))
            ->assertOk()->assertJsonPath('data', []);

        $note = $user->appNotifications()->create([
            'title' => 'Halo',
            'body' => 'Isi',
            'type' => 'general',
        ]);

        $this->getJson('/api/v1/notifications', $this->authHeader($user))
            ->assertOk()->assertJsonPath('data.0.title', 'Halo');

        // Milik orang lain tidak bisa ditandai.
        $this->postJson("/api/v1/notifications/{$note->id}/read", [], $this->authHeader($other))
            ->assertNotFound();

        $this->postJson("/api/v1/notifications/{$note->id}/read", [], $this->authHeader($user))
            ->assertOk();

        $this->assertNotNull($note->fresh()->read_at);
    }

    public function test_thrift_upload_with_photo(): void
    {
        Storage::fake('public');
        $seller = User::factory()->create();
        $category = Category::factory()->create();

        // Multipart (postJson tidak bisa kirim file).
        $response = $this->call('POST', '/api/v1/thrift', [
            'title' => 'Kipas Angin Bekas',
            'description' => 'Masih dingin',
            'price' => 150000,
            'category_id' => $category->id,
            'condition_status' => '85% Layak Pakai',
        ], [], [
            'images' => [UploadedFile::fake()->image('kipas.jpg')],
        ], $this->transformHeadersToServerVars($this->authHeader($seller)));

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertDatabaseHas('thrift_products', [
            'title' => 'Kipas Angin Bekas',
            'seller_id' => $seller->id,
        ]);
    }

    public function test_kos_sort_param_orders_by_price(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        KosListing::factory()->for($owner, 'owner')->create(['price_per_month' => 2000000]);
        KosListing::factory()->for($owner, 'owner')->create(['price_per_month' => 500000]);

        $data = $this->getJson('/api/v1/kos?sort=termurah')
            ->assertOk()->json('data.data');

        $prices = array_map(fn ($row) => (float) $row['price_per_month'], $data);
        $sorted = $prices;
        sort($sorted);
        $this->assertSame($sorted, $prices);
    }
}
