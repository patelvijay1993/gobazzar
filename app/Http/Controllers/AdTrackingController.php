<?php

namespace App\Http\Controllers;

use App\Models\AdStat;
use App\Models\Advertisement;
use Illuminate\Http\Request;

class AdTrackingController extends Controller
{
    public function impression(Request $request, Advertisement $ad)
    {
        AdStat::recordImpression($ad->id);
        return response()->json(['ok' => true]);
    }

    public function click(Request $request, Advertisement $ad)
    {
        AdStat::recordClick($ad->id);
        return redirect()->away($ad->click_url);
    }
}
