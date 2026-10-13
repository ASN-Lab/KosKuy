<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ThriftProduct;
use App\Support\StorefrontCache;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

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
        $page = max(1, (int) $request->query('page', 1));
        $cacheKey = 'index.' . md5(json_encode($filters) . '|p' . $page);

        $categories = StorefrontCache::remember('categories', 'all', fn () => Category::orderBy('name')->get());

        $payload = StorefrontCache::remember('thrift', $cacheKey, function () use ($filters) {
            $query = ThriftProduct::where('is_sold', false)->with(['images', 'category', 'seller']);

            $this->applyFilters($query, $filters);

            match ($filters['sort']) {
                'termurah' => $query->orderBy('price')->orderByDesc('id'),
                'termahal' => $query->orderByDesc('price')->orderByDesc('id'),
                'nama' => $query->orderBy('title')->orderByDesc('id'),
                default => $query->latest(),
            };

            $paginator = $query->paginate(12);

            return [
                'items' => $paginator->getCollection()->values()->all(),
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'category_counts' => $this->categoryCounts($filters),
            ];
        });

        $products = new LengthAwarePaginator(
            $payload['items'],
            $payload['total'],
            $payload['per_page'],
            $payload['current_page'],
            ['path' => Paginator::resolveCurrentPath(), 'query' => $request->query()],
        );

        $activeCategory = $filters['category']
            ? $categories->firstWhere('id', $filters['category'])
            : null;

        $activeFilters = $this->activeFilters($filters, $categories);

        return view('thrift.index', [
            'products' => $products,
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'categoryCounts' => $payload['category_counts'],
            'activeFilters' => $activeFilters,
            'activeFilterCount' => $activeFilters->count(),
        ]);
    }

    public function show($id)
    {
        $payload = StorefrontCache::remember('thrift', 'show.' . $id, function () use ($id) {
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

            return [
                'product' => $product,
                'relatedProducts' => $relatedProducts,
                'sellerProductsCount' => $product->seller
                    ? $product->seller->thriftProducts()->where('is_sold', false)->count()
                    : 0,
            ];
        });

        return view('thrift.show', $payload);
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