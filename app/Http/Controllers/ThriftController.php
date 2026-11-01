<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ThriftProduct;
use Illuminate\Http\Request;

class ThriftController extends Controller
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

        $query = ThriftProduct::with(['images', 'category', 'seller']);

        $this->applyFilters($query, $filters);

        // Sold items stay visible in the catalog, but sorted last.
        $query->orderByDesc('is_sold');

        match ($filters['sort']) {
            'termurah' => $query->orderBy('price')->orderByDesc('id'),
            'termahal' => $query->orderByDesc('price')->orderByDesc('id'),
            'nama' => $query->orderBy('title')->orderByDesc('id'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        $categories = Category::orderBy('name')->get();
        $activeCategory = $filters['category']
            ? $categories->firstWhere('id', $filters['category'])
            : null;

        $activeFilters = $this->activeFilters($filters, $categories);

        return view('thrift.index', [
            'products' => $products,
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'categoryCounts' => $this->categoryCounts($filters),
            'activeFilters' => $activeFilters,
            'activeFilterCount' => $activeFilters->count(),
        ]);
    }

    public function show($id)
    {
        // Sold items stay reachable so shared links never dead-end.
        $product = ThriftProduct::with(['images', 'category', 'seller'])
            ->findOrFail($id);

        $relatedProducts = ThriftProduct::where('is_sold', false)
            ->where('id', '!=', $product->id)
            ->when(
                $product->category_id,
                fn ($query) => $query->where('category_id', $product->category_id)
            )
            ->with(['images', 'category', 'seller'])
            ->latest()
            ->take(3)
            ->get();

        if ($relatedProducts->isEmpty()) {
            $relatedProducts = ThriftProduct::where('is_sold', false)
                ->where('id', '!=', $product->id)
                ->with(['images', 'category', 'seller'])
                ->latest()
                ->take(3)
                ->get();
        }

        return view('thrift.show', compact('product', 'relatedProducts'));
    }

    /**
     * Halaman checkout manual (transfer bank / COD).
     */
    public function checkout($id)
    {
        $product = ThriftProduct::with(['images', 'seller'])->findOrFail($id);

        abort_if($product->is_sold, 410, 'Barang sudah terjual.');

        return view('thrift.checkout', ['product' => $product]);
    }

    /**
     * @return array{search: ?string, category: ?int, min: ?int, max: ?int, available: bool, sort: string}
     */
    private function filters(Request $request): array
    {
        $category = $request->query('category_id');
        $sort = $request->query('sort');

        return [
            'search' => $request->filled('search')
                ? trim((string) $request->query('search'))
                : null,
            'category' => $category !== null && $category !== ''
                ? (int) $category
                : null,
            'min' => $request->filled('min_price') ? max(0, (int) $request->query('min_price')) : null,
            'max' => $request->filled('max_price') ? max(0, (int) $request->query('max_price')) : null,
            'available' => $request->boolean('available'),
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
                    ->orWhere('description', 'like', $term);
            });
        }

        if ($filters['category']) {
            $query->where('category_id', $filters['category']);
        }

        if ($filters['min'] !== null) {
            $query->where('price', '>=', $filters['min']);
        }

        if ($filters['max'] !== null) {
            $query->where('price', '<=', $filters['max']);
        }

        if ($filters['available']) {
            $query->where('is_sold', false);
        }
    }

    /**
     * Badge counts for the category list. The base query already hides sold
     * items, so the "belum terjual" toggle cannot inflate these numbers.
     *
     * @param  array<string, mixed>  $filters
     * @return array<int, int>
     */
    private function categoryCounts(array $filters): array
    {
        $scoped = array_merge($filters, ['category' => null]);

        return ThriftProduct::where('is_sold', false)
            ->selectRaw('category_id, COUNT(*) as total')
            ->when($scoped['search'], function ($query) use ($scoped) {
                $term = '%' . $scoped['search'] . '%';
                $query->where(fn ($inner) => $inner->where('title', 'like', $term)->orWhere('description', 'like', $term));
            })
            ->when($scoped['min'] !== null, fn ($query) => $query->where('price', '>=', $scoped['min']))
            ->when($scoped['max'] !== null, fn ($query) => $query->where('price', '<=', $scoped['max']))
            ->groupBy('category_id')
            ->pluck('total', 'category_id')
            ->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     * @param  \Illuminate\Support\Collection<int, Category>  $categories
     * @return \Illuminate\Support\Collection<int, array{label: string, url: string}>
     */
    private function activeFilters(array $filters, $categories)
    {
        $chips = collect();

        if ($filters['search']) {
            $chips->push([
                'label' => 'Kata kunci: "' . $filters['search'] . '"',
                'url' => route('thrift.index', request()->except(['search', 'page'])),
            ]);
        }

        if ($filters['category']) {
            $category = $categories->firstWhere('id', $filters['category']);

            $chips->push([
                'label' => $category?->name ?? 'Kategori',
                'url' => route('thrift.index', request()->except(['category_id', 'page'])),
            ]);
        }

        if ($filters['min'] !== null || $filters['max'] !== null) {
            $label = $filters['min'] !== null && $filters['max'] !== null
                ? rupiah($filters['min']) . ' - ' . rupiah($filters['max'])
                : rupiah($filters['min'] ?? $filters['max']);

            $chips->push([
                'label' => $label,
                'url' => route('thrift.index', request()->except(['min_price', 'max_price', 'page'])),
            ]);
        }

        if ($filters['available']) {
            $chips->push([
                'label' => 'Belum terjual',
                'url' => route('thrift.index', request()->except(['available', 'page'])),
            ]);
        }

        return $chips;
    }
}