<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * Like the quote, or remove the like if the user already liked it.
     * The user comes back to the same place of the page (the quote of the day box or the quote's card).
     */
    public function toggle(Request $request, Quote $quote): RedirectResponse
    {
        Auth::user()->likedQuotes()->toggle($quote->id);

        $anchor = $request->input('anchor') === 'quote-of-the-day'
            ? 'quote-of-the-day'
            : 'quote-'.$quote->id;

        return back()->withFragment($anchor);
    }
}
