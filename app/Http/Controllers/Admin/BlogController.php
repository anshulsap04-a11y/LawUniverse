<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Blog::with(['category', 'author']);

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $blogs = $query->latest()->paginate(15);
        $categories = BlogCategory::all();

        return view('admin.blogs.index', compact('blogs', 'categories'));
    }

    public function create()
    {
        $categories = BlogCategory::all();
        return view('admin.blogs.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:blog_categories,id',
            'summary' => 'nullable|string',
            'content' => 'required|string',
            'tags' => 'nullable|string',
            'reference' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
            'banner_image' => 'nullable|image|max:5120',
        ]);

        $bannerPath = null;
        if ($request->hasFile('banner_image')) {
            $bannerPath = $request->file('banner_image')->store('blogs/banners', 'public');
        }

        Blog::create([
            'category_id' => $validated['category_id'],
            'author_id' => auth()->id(),
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . Str::random(5),
            'summary' => $validated['summary'] ?? null,
            'content' => $validated['content'],
            'banner_image' => $bannerPath,
            'tags' => $validated['tags'] ?? null,
            'status' => $validated['status'],
            'published_at' => $validated['status'] === 'published' ? now() : null,
        ]);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog article saved successfully.');
    }

    public function statistics()
    {
        $totalBlogs = Blog::count();
        $publishedBlogs = Blog::where('status', 'published')->count();
        $draftBlogs = Blog::where('status', 'draft')->count();
        $totalViews = Blog::sum('views_count');
        $topBlogs = Blog::orderBy('views_count', 'desc')->take(10)->get();

        return view('admin.blogs.statistics', compact('totalBlogs', 'publishedBlogs', 'draftBlogs', 'totalViews', 'topBlogs'));
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();
        return redirect()->route('admin.blogs.index')->with('success', 'Blog article deleted.');
    }

    public function bulkTrash(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:blogs,id',
        ]);

        Blog::whereIn('id', $validated['ids'])->delete();
        return back()->with('success', count($validated['ids']) . ' blog posts moved to trash.');
    }
}
