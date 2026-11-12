<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\BareAct;
use App\Models\LegalMaxim;
use App\Models\LandmarkJudgment;
use App\Models\Blog;
use App\Models\Package;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Public landing page.
     */
    public function index()
    {
        $featuredResources = Resource::where('status', 'published')->latest()->take(6)->get();
        $bareActs = BareAct::latest()->take(4)->get();
        $legalMaxims = LegalMaxim::latest()->take(6)->get();
        $recentBlogs = Blog::where('status', 'published')->latest()->take(3)->get();
        $packages = Package::where('is_active', true)->with('features')->get();

        return view('public.home', compact('featuredResources', 'bareActs', 'legalMaxims', 'recentBlogs', 'packages'));
    }

    /**
     * Public Resources catalog.
     */
    public function resources(Request $request)
    {
        $query = Resource::where('status', 'published')->with('category');

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $resources = $query->latest()->paginate(12);
        return view('public.resources.index', compact('resources'));
    }

    /**
     * Public Bare Acts.
     */
    public function bareActs(Request $request)
    {
        $query = BareAct::query();
        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }
        $acts = $query->paginate(12);
        return view('public.resources.bare-acts', compact('acts'));
    }

    /**
     * Public Legal Maxims.
     */
    public function legalMaxims(Request $request)
    {
        $query = LegalMaxim::query();
        if ($request->filled('search')) {
            $query->where('latin_term', 'like', "%{$request->search}%")
                  ->orWhere('english_meaning', 'like', "%{$request->search}%");
        }
        $maxims = $query->paginate(15);
        return view('public.resources.legal-maxims', compact('maxims'));
    }

    /**
     * Public Landmark Judgments.
     */
    public function landmarkJudgments(Request $request)
    {
        $query = LandmarkJudgment::query();
        if ($request->filled('search')) {
            $query->where('case_title', 'like', "%{$request->search}%")
                  ->orWhere('citation', 'like', "%{$request->search}%");
        }
        $judgments = $query->paginate(12);
        return view('public.resources.landmark-judgments', compact('judgments'));
    }

    /**
     * Public Blogs.
     */
    public function blogs(Request $request)
    {
        $query = Blog::where('status', 'published')->with('author');
        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }
        $blogs = $query->latest()->paginate(9);
        return view('public.blogs.index', compact('blogs'));
    }

    public function blogDetail(string $slug)
    {
        $blog = Blog::where('slug', $slug)->where('status', 'published')->firstOrFail();
        $blog->increment('views_count');
        return view('public.blogs.show', compact('blog'));
    }
}

