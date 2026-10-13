<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PartnerController extends Controller
{
    /**
     * Public landing page for the two partner paths — renting out a kos and
     * selling thrift items. Thrift selling has no backend flow yet, so that
     * track is presented as "coming soon" instead of promising something the
     * platform cannot do.
     */
    public function index(): View
    {
        return view('partner.index');
    }
}
