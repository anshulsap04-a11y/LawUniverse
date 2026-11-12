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

    public function index(Request $request)
    {
        $query = Resource::with(['category', 'uploader']);

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

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->boolean('is_free')) {
            $query->where('is_free', true);
        }

        if ($request->boolean('is_copyright')) {
            $query->where('is_copyright', true);
        }

        if ($request->boolean('my_data')) {
            $query->where('uploaded_by', auth()->id());
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $resources = $query->latest()->paginate(15);
        $categories = ResourceCategory::all();

        return view('admin.resources.index', compact('resources', 'categories'));
    }

    public function create()
    {
        $categories = ResourceCategory::all();
        return view('admin.resources.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:resource_categories,id',
            'resource_type' => 'required|string',
            'description' => 'nullable|string',
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

    public function clone(Resource $resource)
    {
        $cloned = $resource->replicate();
        $cloned->title = $resource->title . ' (Copy)';
        $cloned->slug = Str::slug($cloned->title) . '-' . Str::random(5);
        $cloned->status = 'draft';
        $cloned->save();

        return redirect()->route('admin.resources.index')->with('success', "Resource '{$resource->title}' cloned successfully as a draft.");
    }

    public function destroy(Resource $resource)
    {
        $resource->delete();
        return redirect()->route('admin.resources.index')->with('success', 'Resource moved to trash.');
    }

    public function bulkTrash(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:resources,id',
        ]);

        Resource::whereIn('id', $validated['ids'])->delete();
        return back()->with('success', count($validated['ids']) . ' resources moved to trash.');
    }

    // Bare Acts
    public function bareActs(Request $request)
    {
        $query = BareAct::with(['category', 'creator']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('act_name', 'like', "%{$s}%")
                  ->orWhere('act_number', 'like', "%{$s}%");
            });
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->boolean('is_free')) {
            $query->where('is_free', true);
        }

        if ($request->boolean('is_copyright')) {
            $query->where('is_copyright', true);
        }

        if ($request->boolean('my_data')) {
            $query->where('created_by', auth()->id());
        }

        $acts = $query->latest()->paginate(15);
        $categories = ResourceCategory::all();

        return view('admin.resources.bare-acts', compact('acts', 'categories'));
    }

    public function storeBareAct(Request $request)
    {
        $validated = $request->validate([
            'act_name' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'act_number' => 'nullable|string|max:100',
            'year' => 'required|integer',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:resource_categories,id',
            'content' => 'nullable|string',
            'is_free' => 'nullable|boolean',
            'is_copyright' => 'nullable|boolean',
        ]);

        $actName = $validated['act_name'] ?? $validated['title'] ?? 'Statutory Act';

        BareAct::create([
            'act_name' => $actName,
            'act_number' => $validated['act_number'] ?? null,
            'year' => $validated['year'],
            'description' => $validated['description'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'content' => $validated['content'] ?? null,
            'jurisdiction' => 'Central / National (India)',
            'language' => 'English',
            'is_free' => !empty($validated['is_free']),
            'is_copyright' => !empty($validated['is_copyright']),
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Bare Act added successfully.');
    }

    public function bulkTrashBareActs(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:bare_acts,id',
        ]);

        BareAct::whereIn('id', $validated['ids'])->delete();
        return back()->with('success', count($validated['ids']) . ' Bare Acts moved to trash.');
    }

    // Legal Maxims
    public function legalMaxims(Request $request)
    {
        $query = LegalMaxim::with(['category', 'creator']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('latin_maxim', 'like', "%{$s}%")
                  ->orWhere('title', 'like', "%{$s}%")
                  ->orWhere('english_meaning', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->boolean('my_data')) {
            $query->where('created_by', auth()->id());
        }

        $maxims = $query->latest()->paginate(15);
        $categories = ResourceCategory::all();

        return view('admin.resources.legal-maxims', compact('maxims', 'categories'));
    }

    public function storeLegalMaxim(Request $request)
    {
        $validated = $request->validate([
            'latin_term' => 'nullable|string|max:255',
            'latin_maxim' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'english_meaning' => 'required|string|max:255',
            'category_id' => 'nullable|exists:resource_categories,id',
            'origin' => 'nullable|string|max:100',
            'explanation' => 'nullable|string',
            'legal_explanation' => 'nullable|string',
            'related_cases' => 'nullable|string',
        ]);

        $latin = $validated['latin_maxim'] ?? $validated['latin_term'] ?? 'Maxim';
        $title = $validated['title'] ?? $latin;
        $explanation = $validated['legal_explanation'] ?? $validated['explanation'] ?? '';

        LegalMaxim::create([
            'title' => $title,
            'latin_maxim' => $latin,
            'english_meaning' => $validated['english_meaning'],
            'category_id' => $validated['category_id'] ?? null,
            'description' => $validated['origin'] ?? 'Classical Jurisprudence',
            'legal_explanation' => $explanation,
            'reference' => $validated['related_cases'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Legal Maxim registered successfully.');
    }

    public function bulkTrashLegalMaxims(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:legal_maxims,id',
        ]);

        LegalMaxim::whereIn('id', $validated['ids'])->delete();
        return back()->with('success', count($validated['ids']) . ' Legal Maxims moved to trash.');
    }

    // Landmark Judgments
    public function landmarkJudgments(Request $request)
    {
        $query = LandmarkJudgment::with(['category', 'creator']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('case_name', 'like', "%{$s}%")
                  ->orWhere('citation', 'like', "%{$s}%");
            });
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->boolean('is_free')) {
            $query->where('is_free', true);
        }

        if ($request->boolean('my_data')) {
            $query->where('created_by', auth()->id());
        }

        $judgments = $query->latest()->paginate(15);
        $categories = ResourceCategory::all();

        return view('admin.resources.landmark-judgments', compact('judgments', 'categories'));
    }

    public function storeLandmarkJudgment(Request $request)
    {
        $validated = $request->validate([
            'case_name' => 'nullable|string|max:255',
            'case_title' => 'nullable|string|max:255',
            'citation' => 'required|string|max:255',
            'court_name' => 'nullable|string|max:255',
            'court' => 'nullable|string|max:255',
            'judgment_date' => 'required|date',
            'bench' => 'nullable|string|max:255',
            'summary' => 'nullable|string',
            'case_summary' => 'nullable|string',
            'held' => 'nullable|string',
            'legal_principle' => 'nullable|string',
            'is_free' => 'nullable|boolean',
        ]);

        $caseName = $validated['case_name'] ?? $validated['case_title'] ?? 'Precedent';
        $court = $validated['court'] ?? $validated['court_name'] ?? 'Supreme Court of India';
        $summary = $validated['case_summary'] ?? $validated['summary'] ?? '';
        $principle = $validated['legal_principle'] ?? $validated['held'] ?? '';

        LandmarkJudgment::create([
            'case_name' => $caseName,
            'court' => $court,
            'citation' => $validated['citation'],
            'judgment_date' => $validated['judgment_date'],
            'bench' => $validated['bench'] ?? 'Division Bench',
            'legal_principle' => $principle,
            'case_summary' => $summary,
            'is_free' => !empty($validated['is_free']),
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Landmark Judgment recorded successfully.');
    }

    public function bulkTrashLandmarkJudgments(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:landmark_judgments,id',
        ]);

        LandmarkJudgment::whereIn('id', $validated['ids'])->delete();
        return back()->with('success', count($validated['ids']) . ' judgments moved to trash.');
    }

    // Notes
    public function notes(Request $request)
    {
        $query = Note::with('user');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('subject', 'like', "%{$s}%");
            });
        }

        if ($request->boolean('my_data')) {
            $query->where('user_id', auth()->id());
        }

        $notes = $query->latest()->paginate(15);
        $categories = ResourceCategory::all();

        return view('admin.resources.notes', compact('notes', 'categories'));
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

    public function bulkTrashNotes(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:notes,id',
        ]);

        Note::whereIn('id', $validated['ids'])->delete();
        return back()->with('success', count($validated['ids']) . ' notes moved to trash.');
    }
}
