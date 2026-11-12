<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Examination;
use App\Models\ExaminationCategory;
use App\Models\ExaminationLabel;
use App\Models\Question;
use App\Models\PreviousYearPaper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExaminationController extends Controller
{
    /**
     * Display a listing of examinations.
     */
    public function index(Request $request)
    {
        $query = Examination::with(['category', 'label'])->withCount('questions');

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $examinations = $query->latest()->paginate(15);
        $categories = ExaminationCategory::all();
        $labels = ExaminationLabel::all();

        return view('admin.examinations.index', compact('examinations', 'categories', 'labels'));
    }

    /**
     * Show form to create examination.
     */
    public function create()
    {
        $categories = ExaminationCategory::all();
        $labels = ExaminationLabel::all();
        $questions = Question::all();

        return view('admin.examinations.create', compact('categories', 'labels', 'questions'));
    }

    /**
     * Store examination and link questions.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:examination_categories,id',
            'label_id' => 'nullable|exists:examination_labels,id',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'total_marks' => 'required|numeric|min:1',
            'passing_percentage' => 'required|numeric|min:1|max:100',
            'is_paid' => 'nullable|boolean',
            'price' => 'nullable|numeric|min:0',
            'is_published' => 'nullable|boolean',
            'selected_questions' => 'nullable|array',
            'selected_questions.*' => 'exists:questions,id',
        ]);

        $exam = Examination::create([
            'category_id' => $validated['category_id'],
            'label_id' => $validated['label_id'] ?? null,
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . Str::random(5),
            'description' => $validated['description'] ?? null,
            'duration_minutes' => $validated['duration_minutes'],
            'total_marks' => $validated['total_marks'],
            'passing_percentage' => $validated['passing_percentage'],
            'is_paid' => !empty($validated['is_paid']),
            'price' => $validated['price'] ?? 0.00,
            'is_published' => !empty($validated['is_published']),
        ]);

        if (!empty($validated['selected_questions'])) {
            $syncData = [];
            foreach ($validated['selected_questions'] as $idx => $qId) {
                $syncData[$qId] = ['order_no' => $idx + 1];
            }
            $exam->questions()->sync($syncData);
        }

        return redirect()->route('admin.examinations.index')->with('success', 'Examination created successfully with linked questions.');
    }

    /**
     * Question bank listing.
     */
    public function questions(Request $request)
    {
        $query = Question::with('category');

        if ($request->filled('search')) {
            $query->where('question_text', 'like', "%{$request->search}%");
        }

        $questions = $query->latest()->paginate(15);
        $categories = ExaminationCategory::all();

        return view('admin.examinations.questions', compact('questions', 'categories'));
    }

    /**
     * Store new question in question bank.
     */
    public function storeQuestion(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:examination_categories,id',
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_option' => 'required|in:A,B,C,D',
            'explanation' => 'nullable|string',
            'marks' => 'required|numeric|min:0.5',
            'negative_marks' => 'nullable|numeric|min:0',
            'difficulty' => 'required|in:easy,medium,hard',
        ]);

        Question::create($validated);

        return back()->with('success', 'Question added to Question Bank.');
    }

    /**
     * Previous Year Papers.
     */
    public function previousYearPapers()
    {
        $papers = PreviousYearPaper::with('category')->latest()->paginate(15);
        $categories = ExaminationCategory::all();

        return view('admin.examinations.previous-years', compact('papers', 'categories'));
    }

    public function storePreviousYearPaper(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:examination_categories,id',
            'title' => 'required|string|max:255',
            'year' => 'required|integer',
            'exam_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'paper_file' => 'nullable|file|max:51200',
        ]);

        $filePath = null;
        if ($request->hasFile('paper_file')) {
            $filePath = $request->file('paper_file')->store('examinations/papers', 'public');
        }

        PreviousYearPaper::create([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . Str::random(4),
            'year' => $validated['year'],
            'exam_name' => $validated['exam_name'],
            'description' => $validated['description'] ?? null,
            'file_path' => $filePath,
        ]);

        return back()->with('success', 'Previous Year Question Paper catalogued successfully.');
    }

    /**
     * Categories & Labels management.
     */
    public function categoriesAndLabels()
    {
        $categories = ExaminationCategory::all();
        $labels = ExaminationLabel::all();

        return view('admin.examinations.categories-labels', compact('categories', 'labels'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:examination_categories,name',
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        ExaminationCategory::create($validated);

        return back()->with('success', 'Examination category created.');
    }

    public function storeLabel(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:examination_labels,name',
            'color' => 'nullable|string|max:30',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        ExaminationLabel::create($validated);

        return back()->with('success', 'Examination label created.');
    }
}
