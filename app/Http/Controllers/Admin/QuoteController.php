<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Quote::with('category')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('quote_text', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $quotes     = $query->paginate(12)->withQueryString();
        $categories = Category::all();
        return view('admin.quotes.index', compact('quotes', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.quotes.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'           => 'required|string|max:200',
            'quote_text'      => 'required|string',
            'author'          => 'nullable|string|max:100',
            'category_id'     => 'required|exists:categories,id',
            'image'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'text_color'      => 'required|string|max:20',
            'text_position'   => 'required|string',
            'font_size'       => 'required|string',
            'font_style'      => 'required|string',
            'overlay_opacity' => 'required|integer|min:0|max:100',
            'overlay_color'   => 'required|string|max:20',
            'status'          => 'required|in:publish,draft',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('quotes', 'public');
        }

        $data['slug'] = Str::slug($data['title']) . '-' . Str::random(5);
        Quote::create($data);

        return redirect()->route('admin.quotes.index')->with('success', 'Quote created successfully!');
    }

    public function edit(Quote $quote)
    {
        $categories = Category::all();
        return view('admin.quotes.edit', compact('quote', 'categories'));
    }

    public function update(Request $request, Quote $quote)
    {
        $data = $request->validate([
            'title'           => 'required|string|max:200',
            'quote_text'      => 'required|string',
            'author'          => 'nullable|string|max:100',
            'category_id'     => 'required|exists:categories,id',
            'image'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'text_color'      => 'required|string|max:20',
            'text_position'   => 'required|string',
            'font_size'       => 'required|string',
            'font_style'      => 'required|string',
            'overlay_opacity' => 'required|integer|min:0|max:100',
            'overlay_color'   => 'required|string|max:20',
            'status'          => 'required|in:publish,draft',
        ]);

        if ($request->hasFile('image')) {
            if ($quote->image) {
                Storage::disk('public')->delete($quote->image);
            }
            $data['image'] = $request->file('image')->store('quotes', 'public');
        }

        $quote->update($data);
        return redirect()->route('admin.quotes.index')->with('success', 'Quote updated successfully!');
    }

    public function destroy(Quote $quote)
    {
        if ($quote->image) {
            Storage::disk('public')->delete($quote->image);
        }
        $quote->delete();
        return redirect()->route('admin.quotes.index')->with('success', 'Quote deleted!');
    }

    public function preview(Quote $quote)
    {
        return view('admin.quotes.preview', compact('quote'));
    }

    public function toggleStatus(Quote $quote)
    {
        $quote->update(['status' => $quote->status === 'publish' ? 'draft' : 'publish']);
        return response()->json(['status' => $quote->status]);
    }
}
