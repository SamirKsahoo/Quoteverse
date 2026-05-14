<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Quote;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_quotes'     => Quote::count(),
            'published_quotes' => Quote::where('status', 'publish')->count(),
            'draft_quotes'     => Quote::where('status', 'draft')->count(),
            'total_categories' => Category::count(),
            'total_views'      => Quote::sum('views'),
        ];

        $recentQuotes = Quote::with('category')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $topCategories = Category::withCount(['quotes as published_quotes_count' => function ($q) {
            $q->where('status', 'publish');
        }])->orderBy('published_quotes_count', 'desc')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentQuotes', 'topCategories'));
    }
}
