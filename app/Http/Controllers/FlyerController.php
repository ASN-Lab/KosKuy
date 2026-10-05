<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\KosListing;
use App\Models\ThriftProduct;
use Illuminate\View\View;

class FlyerController extends Controller
{
    /**
     * Printable A5 promo sheets. The live counters come straight from the
     * database so a printed flyer can never advertise numbers the site
     * does not actually have.
     */
    public function __invoke(): View
    {
        $stats = [
            'kos' => KosListing::where('is_available', true)->count(),
            'thrift' => ThriftProduct::where('is_sold', false)->count(),
            'categories' => Category::count(),
        ];

        return view('flyer.index', compact('stats'));
    }
}