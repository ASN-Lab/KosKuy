<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\KosListing;
use App\Models\ThriftProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Covers the public storefront: every page renders, and the Kos/Thrift filters
 * actually narrow the result set instead of silently returning everything.
 */
class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_with_an_empty_database(): void
    {
        // With nothing to show, the featured sections hide themselves entirely
        // rather than rendering empty carousels.
        $this->get('/')
            ->assertOk()
            ->assertDontSee('id="kos-terbaru"', false)
            ->assertDontSee('id="thrift-terbaru"', false)
            ->assertSee('id="hero"', false)
            ->assertSee('id="faq"', false);
    }

    public function test_home_page_renders_with_content(): void
    {
        $owner = User::factory()->create(['name' => 'Bu Sari']);

        $kos = KosListing::factory()->for($owner, 'owner')->create(['title' => 'Kos Rama Rama']);
        $thrift = ThriftProduct::factory()->create(['title' => 'Topi Bekas']);

        $this->get('/')
            ->assertOk()
            ->assertSee('KosKuy')
            ->assertSee('Bu Sari')
            ->assertSee($kos->title, false)
            ->assertSee($thrift->title, false);
    }

    public function test_public_pages_render(): void
    {
        foreach (['/', '/kos', '/thrift', '/login', '/register'] as $uri) {
            $this->get($uri)->assertOk();
        }
    }

    public function test_kos_index_lists_only_available_listings(): void
    {
        $available = KosListing::factory()->create(['title' => 'Kos Tersedia']);
        $taken = KosListing::factory()->unavailable()->create(['title' => 'Kos Penuh']);

        $this->get('/kos')
            ->assertOk()
            ->assertSee($available->title, false)
            ->assertDontSee($taken->title, false);
    }

    public function test_kos_index_filters_by_search_gender_and_price(): void
    {
        $putri = KosListing::factory()->putri()->create([
            'title' => 'Kos Putri Melati',
            'price_per_month' => 1200000,
        ]);

        $putra = KosListing::factory()->putra()->create([
            'title' => 'Kos Putra Kenari',
            'price_per_month' => 2500000,
        ]);

        $this->get('/kos?search=Melati')
            ->assertOk()
            ->assertSee($putri->title, false)
            ->assertDontSee($putra->title, false);

        $this->get('/kos?gender_type=putra')
            ->assertOk()
            ->assertSee($putra->title, false)
            ->assertDontSee($putri->title, false);

        $this->get('/kos?max_price=1500000')
            ->assertOk()
            ->assertSee($putri->title, false)
            ->assertDontSee($putra->title, false);
    }

    public function test_kos_index_filters_by_facility(): void
    {
        $withWifi = KosListing::factory()->withFacilities(['WiFi'])->create(['title' => 'Kos Wifi']);
        $without = KosListing::factory()->withFacilities(['Parkir'])->create(['title' => 'Kos Parkir']);

        $this->get('/kos?facilities[]=WiFi')
            ->assertOk()
            ->assertSee($withWifi->title, false)
            ->assertDontSee($without->title, false);
    }

    public function test_kos_index_supports_both_sort_orders(): void
    {
        $cheap = KosListing::factory()->create(['title' => 'Kos Murah', 'price_per_month' => 700000]);
        $pricey = KosListing::factory()->create(['title' => 'Kos Mahal', 'price_per_month' => 3200000]);

        $ascending = $this->get('/kos?sort=termurah')->assertOk();
        $this->assertLessThan(
            strpos($ascending->getContent(), $pricey->title),
            strpos($ascending->getContent(), $cheap->title)
        );

        $descending = $this->get('/kos?sort=termahal')->assertOk();
        $this->assertLessThan(
            strpos($descending->getContent(), $cheap->title),
            strpos($descending->getContent(), $pricey->title)
        );
    }

    public function test_kos_detail_shows_listing_and_similar_items(): void
    {
        $kos = KosListing::factory()->withFacilities()->create(['title' => 'Kos Unik Syahputra']);

        $this->get('/kos/'.$kos->id)
            ->assertOk()
            ->assertSee('Kos Unik Syahputra', false)
            ->assertSee('WiFi', false);
    }

    public function test_kos_detail_returns_404_for_unknown_id(): void
    {
        $this->get('/kos/999999')->assertNotFound();
    }

    public function test_thrift_index_shows_all_products_but_tags_sold_ones(): void
    {
        $available = ThriftProduct::factory()->create(['title' => 'Buku Still Available']);
        $sold = ThriftProduct::factory()->sold()->create(['title' => 'Buku Sudah Terjual']);

        $this->get('/thrift')
            ->assertOk()
            ->assertSee($available->title, false)
            ->assertSee($sold->title, false)
            ->assertSee('Terjual', false);
    }

    public function test_thrift_index_filters_by_category(): void
    {
        $category = Category::factory()->create(['name' => 'Buku', 'slug' => 'buku']);
        $other = Category::factory()->create(['name' => 'Elektronik', 'slug' => 'elektronik']);

        $book = ThriftProduct::factory()->for($category)->create(['title' => 'Novel Bekas']);
        $phone = ThriftProduct::factory()->for($other)->create(['title' => 'Handphone Bekas']);

        $this->get('/thrift?category_id='.$category->id)
            ->assertOk()
            ->assertSee($book->title, false)
            ->assertDontSee($phone->title, false);
    }

    public function test_thrift_detail_is_still_reachable_after_the_item_is_sold(): void
    {
        // A shared link from before the sale should keep working.
        $product = ThriftProduct::factory()->sold()->create(['title' => 'Barang Terjual']);

        $this->get('/thrift/'.$product->id)
            ->assertOk()
            ->assertSee('Barang Terjual', false);
    }

    public function test_thrift_detail_returns_404_for_unknown_id(): void
    {
        $this->get('/thrift/999999')->assertNotFound();
    }

    public function test_home_page_exposes_every_advertised_anchor_target(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        // Every in-page link the footer and navbar use must resolve to a target.
        preg_match_all('/href="#([\w-]+)"/', $html, $matches);

        $this->assertNotEmpty($matches[1], 'expected the home page to link to its own sections');

        foreach (array_unique($matches[1]) as $anchor) {
            $this->assertStringContainsString(
                'id="'.$anchor.'"',
                $html,
                "anchor #{$anchor} has no matching id on the home page"
            );
        }
    }

    public function test_api_endpoints_return_the_documented_envelope(): void
    {
        $kos = KosListing::factory()->create();

        $this->getJson('/api/v1/categories')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->getJson('/api/v1/kos')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->getJson('/api/v1/kos/'.$kos->id)
            ->assertOk()
            ->assertJsonPath('data.title', $kos->title);

        $this->getJson('/api/v1/kos/999999')
            ->assertNotFound()
            ->assertJsonPath('success', false);

        $this->getJson('/api/v1/thrift')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->getJson('/api/v1/thrift/999999')
            ->assertNotFound()
            ->assertJsonPath('success', false);
    }
}