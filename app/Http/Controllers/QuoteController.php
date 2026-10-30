<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuoteRequest;
use App\Models\Era;
use App\Models\Quote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class QuoteController extends Controller
{
    /**
     * Show the quotes (newest first), optionally only the ones of one era,
     * and the quote of the day on the home page.
     */
    public function index(): View
    {
        $eras = Era::query()->withCount('quotes')->orderBy('id')->get();

        $currentEra = $eras->firstWhere('slug', request('era'));
        abort_if(request()->filled('era') && ! $currentEra, 404);

        $allQuotes = Quote::query()
            ->with(['user', 'era'])
            ->withCount('likedBy')
            ->latest()
            ->get();

        // IDs of the quotes the current user liked (empty for guests).
        $likedQuoteIds = Auth::user()?->likedQuotes()->pluck('quotes.id') ?? collect();

        return view('welcome', [
            'page' => 'Home',
            'eras' => $eras,
            'currentEra' => $currentEra,
            'totalQuotes' => $allQuotes->count(),
            'quotes' => $currentEra ? $allQuotes->where('era_id', $currentEra->id)->values() : $allQuotes,
            'likedQuoteIds' => $likedQuoteIds,
            'quoteOfTheDay' => $currentEra ? null : $this->pickQuoteOfTheDay($allQuotes),
        ]);
    }

    /**
     * Share a new quote.
     */
    public function store(QuoteRequest $request): RedirectResponse
    {
        Auth::user()->quotes()->create($request->validated());

        return back();
    }

    /**
     * Edit one of my quotes.
     */
    public function update(QuoteRequest $request, Quote $quote): RedirectResponse
    {
        abort_if($quote->user_id !== Auth::id(), 403);

        $quote->update($request->validated());

        return redirect()->route('home')->withFragment('quote-'.$quote->id);
    }

    /**
     * Delete one of my quotes (immediately, no confirmation).
     */
    public function destroy(Quote $quote): RedirectResponse
    {
        abort_if($quote->user_id !== Auth::id(), 403);

        $quote->delete();

        return back();
    }

    /**
     * The quote of the day is the most liked quote.
     * When several quotes share the highest like count, one of them is picked
     * based on today's date, so everyone sees the same quote all day long and
     * it changes the next day.
     *
     * @param  Collection<int, Quote>  $quotes  quotes loaded with their liked_by_count
     */
    private function pickQuoteOfTheDay(Collection $quotes): ?Quote
    {
        if ($quotes->isEmpty()) {
            return null;
        }

        $highestLikeCount = $quotes->max('liked_by_count');

        $mostLikedQuotes = $quotes
            ->where('liked_by_count', $highestLikeCount)
            ->sortBy('id')
            ->values();

        return $mostLikedQuotes[crc32(today()->toDateString()) % $mostLikedQuotes->count()];
    }
}
