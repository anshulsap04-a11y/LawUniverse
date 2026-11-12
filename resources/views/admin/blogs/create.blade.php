@extends('layouts.admin', ['title' => 'Compose Blog - Law Universe'])

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.blogs.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="bi bi-arrow-left me-1"></i> Back to Blogs
    </a>
    <h4 class="fw-bold mb-1"><i class="bi bi-pencil-square text-success me-2"></i> Compose Legal Article</h4>
    <p class="text-muted small mb-0">Write comprehensive case commentary, doctrinal analysis, or editorial essays.</p>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Article Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" required placeholder="e.g. Navigating Bail Jurisprudence under the Bharatiya Nagarik Suraksha Sanhita">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select" required>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Short Synopsis / Excerpt</label>
                    <textarea name="summary" rows="2" class="form-control" placeholder="A concise 1-2 sentence overview for previews and search engines..."></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Full Article Body <span class="text-danger">*</span></label>
                    <textarea name="content" rows="12" class="form-control font-monospace" required placeholder="Write article content in Markdown or HTML..."></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Banner Image</label>
                    <input type="file" name="banner_image" class="form-control" accept="image/*">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tags (Comma-separated)</label>
                    <input type="text" name="tags" class="form-control" placeholder="BNSS, Bail, Supreme Court">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Publication Status</label>
                    <select name="status" class="form-select">
                        <option value="published" selected>Publish Immediately</option>
                        <option value="draft">Save as Draft</option>
                    </select>
                </div>

                <div class="col-12 mt-4 pt-3 border-top d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success fw-bold px-4">
                        <i class="bi bi-send me-1"></i> Save & Publish Article
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
