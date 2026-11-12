<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\BareAct;
use App\Models\LegalMaxim;
use App\Models\LandmarkJudgment;
use App\Models\Note;
use App\Services\ResourceService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ResourceController extends Controller
{
    protected ResourceService $resourceService;

    public function __construct(ResourceService $resourceService)
    {
        $this->resourceService = $resourceService;
    }

    /**
     * Display a listing of general resources.
     */
    public function index(Request $request)
    {
        $query = Resource::with(['category', 'user']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('author', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $resources = $query->latest()->paginate(15);
        $categories = ResourceCategory::where('is_active', true)->get();

        return view('admin.resources.index', compact('resources', 'categories'));
    }

    /**
     * Show form for uploading a new resource.
     */
    public function create()
    {
        $categories = ResourceCategory::where('is_active', true)->get();
        return view('admin.resources.create', compact('categories'));
    }

    /**
     * Store newly created resource.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:resource_categories,id',
            'resource_type' => 'required|string',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'is_free' => 'nullable|boolean',
            'is_copyright' => 'nullable|boolean',
            'reference_link' => 'nullable|url',
            'author' => 'nullable|string|max:255',
            'year' => 'nullable|string|max:10',
            'language' => 'nullable|string|max:50',
            'status' => 'required|in:draft,published,archived',
            'file' => 'nullable|file|max:51200',
            'thumbnail' => 'nullable|image|max:5120',
        ]);

        $this->resourceService->storeResource(
            $validated,
            $request->file('file'),
            $request->file('thumbnail'),
            auth()->user()
        );

        return redirect()->route('admin.resources.index')->with('success', 'Resource uploaded successfully!');
    }

    /**
     * Clone a resource for quick duplication.
     */
    public function clone(Resource $resource)
    {
        $cloned = $resource->replicate([
            'downloads_count',
            'views_count',
            'created_at',
            'updated_at'
        ]);
        $cloned->title = $resource->title . ' (Copy)';
        $cloned->slug = Str::slug($cloned->title) . '-' . Str::random(5);
        $cloned->status = 'draft';
        $cloned->save();

        return redirect()->route('admin.resources.index')->with('success', "Resource '{$resource->title}' cloned successfully as a draft.");
    }

    /**
     * Delete resource.
     */
    public function destroy(Resource $resource)
    {
        $resource->delete();
        return redirect()->route('admin.resources.index')->with('success', 'Resource moved to trash.');
    }

    /**
     * Bare Acts management.
     */
    public function bareActs(Request $request)
    {
        $acts = BareAct::latest()->paginate(15);
        return view('admin.resources.bare-acts', compact('acts'));
    }

    public function storeBareAct(Request $request)
    {
        $validated = $request->validate([
            'act_number' => 'nullable|string|max:100',
            'title' => 'required|string|max:255',
            'year' => 'required|integer',
            'enactment_date' => 'nullable|date',
            'description' => 'nullable|string',
            'total_sections' => 'nullable|integer|min:0',
            'total_chapters' => 'nullable|integer|min:0',
            'full_text' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(4);
        BareAct::create($validated);

        return back()->with('success', 'Bare Act added successfully.');
    }

    /**
     * Legal Maxims management.
     */
    public function legalMaxims(Request $request)
    {
        $maxims = LegalMaxim::latest()->paginate(15);
        return view('admin.resources.legal-maxims', compact('maxims'));
    }

    public function storeLegalMaxim(Request $request)
    {
        $validated = $request->validate([
            'latin_term' => 'required|string|max:255',
            'english_meaning' => 'required|string|max:255',
            'origin' => 'nullable|string|max:100',
            'explanation' => 'required|string',
            'related_cases' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['latin_term']);
        LegalMaxim::create($validated);

        return back()->with('success', 'Legal Maxim registered successfully.');
    }

    /**
     * Landmark Judgments management.
     */
    public function landmarkJudgments(Request $request)
    {
        $judgments = LandmarkJudgment::latest()->paginate(15);
        return view('admin.resources.landmark-judgments', compact('judgments'));
    }

    public function storeLandmarkJudgment(Request $request)
    {
        $validated = $request->validate([
            'case_title' => 'required|string|max:255',
            'citation' => 'required|string|max:255',
            'court_name' => 'required|string|max:255',
            'judgment_date' => 'required|date',
            'bench' => 'nullable|string|max:255',
            'legal_topic' => 'nullable|string|max:255',
            'summary' => 'required|string',
            'held' => 'required|string',
        ]);

        $validated['slug'] = Str::slug($validated['case_title']) . '-' . Str::random(4);
        LandmarkJudgment::create($validated);

        return back()->with('success', 'Landmark Judgment recorded successfully.');
    }

    /**
     * Notes management.
     */
    public function notes(Request $request)
    {
        $notes = Note::with('user')->latest()->paginate(15);
        return view('admin.resources.notes', compact('notes'));
    }

    public function storeNote(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
            'is_shared' => 'nullable|boolean',
        ]);

        Note::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'subject' => $validated['subject'] ?? null,
            'content' => $validated['content'],
            'is_shared' => !empty($validated['is_shared']),
        ]);

        return back()->with('success', 'Legal Note created successfully.');
    }
}
