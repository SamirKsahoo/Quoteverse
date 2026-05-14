<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Quote;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        $featuredQuotes = Quote::with('category')
            ->where('status', 'publish')
            ->orderBy('views', 'desc')
            ->take(6)
            ->get();

        // PostgreSQL fix: cannot use alias in HAVING, filter in PHP instead
        $categories = Category::withCount(['quotes as published_count' => fn($q) => $q->where('status', 'publish')])
            ->get()
            ->filter(fn($cat) => $cat->published_count > 0)
            ->values();

        $latestQuotes = Quote::with('category')
            ->where('status', 'publish')
            ->orderBy('created_at', 'desc')
            ->take(12)
            ->get();

        return view('pages.home', compact('featuredQuotes', 'categories', 'latestQuotes'));
    }

    public function quotes(Request $request)
    {
        $query = Quote::with('category')->where('status', 'publish');

        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // PostgreSQL fix: use ILIKE instead of LIKE for case-insensitive search
                $q->where('title', 'ilike', '%' . $search . '%')
                  ->orWhere('quote_text', 'ilike', '%' . $search . '%')
                  ->orWhere('author', 'ilike', '%' . $search . '%');
            });
        }

        $quotes     = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();
        $categories = Category::withCount(['quotes as published_count' => fn($q) => $q->where('status', 'publish')])->get();

        return view('pages.quotes', compact('quotes', 'categories'));
    }

    public function show(string $slug)
    {
        $quote = Quote::with('category')
            ->where('slug', $slug)
            ->where('status', 'publish')
            ->firstOrFail();

        $quote->incrementViews();

        $related = Quote::with('category')
            ->where('category_id', $quote->category_id)
            ->where('status', 'publish')
            ->where('id', '!=', $quote->id)
            ->take(4)
            ->get();

        return view('pages.show', compact('quote', 'related'));
    }

    public function category(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $quotes = Quote::with('category')
            ->where('category_id', $category->id)
            ->where('status', 'publish')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $categories = Category::withCount(['quotes as published_count' => fn($q) => $q->where('status', 'publish')])->get();

        return view('pages.category', compact('category', 'quotes', 'categories'));
    }
}