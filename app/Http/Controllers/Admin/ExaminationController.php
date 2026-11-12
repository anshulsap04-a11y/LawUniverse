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
    public function index(Request $request)
    {
        $query = Examination::with(['category', 'firstLabel', 'secondLabel'])->withCount('questions');

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('label_id')) {
            $query->where(function($q) use ($request) {
                $q->where('first_label_id', $request->label_id)
                  ->orWhere('second_label_id', $request->label_id);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        $examinations = $query->latest()->paginate(15);
        $categories = ExaminationCategory::all();
        $labels = ExaminationLabel::all();

        return view('admin.examinations.index', compact('examinations', 'categories', 'labels'));
    }

    public function create()
    {
        $categories = ExaminationCategory::all();
        $labels = ExaminationLabel::all();
        $questions = Question::all();

        return view('admin.examinations.create', compact('categories', 'labels', 'questions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:examination_categories,id',
            'first_label_id' => 'nullable|exists:examination_labels,id',
            'second_label_id' => 'nullable|exists:examination_labels,id',
            'instructions' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'total_marks' => 'required|numeric|min:1',
            'cutoff_percentage' => 'required|numeric|min:1|max:100',
            'selected_questions' => 'nullable|array',
            'selected_questions.*' => 'exists:questions,id',
        ]);

        $exam = Examination::create([
            'exam_code' => 'EX-' . strtoupper(Str::random(6)),
            'title' => $validated['title'],
            'category_id' => $validated['category_id'],
            'first_label_id' => $validated['first_label_id'] ?? null,
            'second_label_id' => $validated['second_label_id'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
            'duration_minutes' => $validated['duration_minutes'],
            'total_marks' => $validated['total_marks'],
            'cutoff_percentage' => $validated['cutoff_percentage'],
            'status' => 'published',
            'created_by' => auth()->id(),
        ]);

        if (!empty($validated['selected_questions'])) {
            $syncData = [];
            foreach ($validated['selected_questions'] as $idx => $qId) {
                $syncData[$qId] = ['order' => $idx + 1];
            }
            $exam->questions()->sync($syncData);
        }

        return redirect()->route('admin.examinations.index')->with('success', 'Examination created successfully with linked questions.');
    }

    public function destroy(Examination $examination)
    {
        $examination->delete();
        return back()->with('success', 'Examination deleted.');
    }

    public function bulkTrash(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:examinations,id',
        ]);

        Examination::whereIn('id', $validated['ids'])->delete();
        return back()->with('success', count($validated['ids']) . ' examinations moved to trash.');
    }

    // Question Bank
    public function questions(Request $request)
    {
        $query = Question::with('category');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('question_title', 'like', "%{$s}%")
                  ->orWhere('question_code', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->boolean('my_data')) {
            $query->where('created_by', auth()->id());
        }

        $questions = $query->latest()->paginate(15);
        $categories = ExaminationCategory::all();
        $labels = ExaminationLabel::all();

        return view('admin.examinations.questions', compact('questions', 'categories', 'labels'));
    }

    public function storeQuestion(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'nullable|exists:examination_categories,id',
            'question_title' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D',
            'explanation' => 'nullable|string',
            'marks' => 'required|numeric|min:0.5',
            'negative_marks' => 'nullable|numeric|min:0',
            'difficulty' => 'required|in:easy,medium,hard',
        ]);

        $options = [
            ['id' => 'A', 'text' => $validated['option_a']],
            ['id' => 'B', 'text' => $validated['option_b']],
            ['id' => 'C', 'text' => $validated['option_c']],
            ['id' => 'D', 'text' => $validated['option_d']],
        ];

        Question::create([
            'question_code' => 'Q-' . strtoupper(Str::random(6)),
            'question_title' => $validated['question_title'],
            'question_type' => 'mcq',
            'options' => $options,
            'correct_answer' => $validated['correct_answer'],
            'explanation' => $validated['explanation'] ?? null,
            'marks' => $validated['marks'],
            'negative_marks' => $validated['negative_marks'] ?? 0.00,
            'category_id' => $validated['category_id'] ?? null,
            'difficulty' => $validated['difficulty'],
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Question added to Question Bank.');
    }

    public function bulkTrashQuestions(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:questions,id',
        ]);

        Question::whereIn('id', $validated['ids'])->delete();
        return back()->with('success', count($validated['ids']) . ' questions moved to trash.');
    }

    // Previous Year Papers
    public function previousYearPapers(Request $request)
    {
        $query = PreviousYearPaper::with('category');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('exam_name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $papers = $query->latest()->paginate(15);
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
            'subject' => 'nullable|string|max:255',
            'paper_file' => 'nullable|file|max:51200',
        ]);

        $filePath = null;
        if ($request->hasFile('paper_file')) {
            $filePath = $request->file('paper_file')->store('examinations/papers', 'public');
        }

        PreviousYearPaper::create([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'year' => $validated['year'],
            'exam_name' => $validated['exam_name'],
            'subject' => $validated['subject'] ?? null,
            'file_path' => $filePath,
            'is_free' => true,
        ]);

        return back()->with('success', 'Previous Year Question Paper catalogued successfully.');
    }

    public function bulkTrashPreviousYears(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:previous_year_papers,id',
        ]);

        PreviousYearPaper::whereIn('id', $validated['ids'])->delete();
        return back()->with('success', count($validated['ids']) . ' past papers moved to trash.');
    }

    // Categories & Labels
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
            'label_type' => 'required|in:First Label,Second Label',
            'description' => 'nullable|string',
        ]);

        ExaminationLabel::create($validated);
        return back()->with('success', 'Examination label created.');
    }

    public function bulkTrashLabels(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:examination_labels,id',
        ]);

        ExaminationLabel::whereIn('id', $validated['ids'])->delete();
        return back()->with('success', count($validated['ids']) . ' labels removed.');
    }
}
