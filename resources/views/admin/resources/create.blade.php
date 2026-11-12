@extends('layouts.admin', ['title' => 'Upload Resource - Law Universe'])

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.resources.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="bi bi-arrow-left me-1"></i> Back to Resources
    </a>
    <h4 class="fw-bold mb-1"><i class="bi bi-cloud-arrow-up text-primary me-2"></i> Upload Legal Resource</h4>
    <p class="text-muted small mb-0">Fill in the resource metadata, access rules, and upload PDF/document files.</p>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        <form action="{{ route('admin.resources.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Resource Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required placeholder="e.g. Constitutional Law Landmark Summaries 2026">
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Resource Type</label>
                    <select name="resource_type" class="form-select">
                        <option value="bare_act">Bare Act</option>
                        <option value="case_law">Case Law</option>
                        <option value="judgment">Judgment</option>
                        <option value="notes" selected>Legal Notes</option>
                        <option value="article">Research Article</option>
                        <option value="question_paper">Question Paper</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Author / Organization</label>
                    <input type="text" name="author" class="form-control" value="{{ old('author', 'Law Universe Editorial') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">Year</label>
                    <input type="number" name="year" class="form-control" value="{{ old('year', date('Y')) }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">Language</label>
                    <input type="text" name="language" class="form-control" value="{{ old('language', 'English') }}">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Resource Description</label>
                    <textarea name="description" rows="3" class="form-control" placeholder="Brief synopsis or summary...">{{ old('description') }}</textarea>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Full Statutory / Analytical Content (Optional if uploading file)</label>
                    <textarea name="content" rows="6" class="form-control font-monospace" placeholder="Markdown or structured legal text...">{{ old('content') }}</textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Attach Document / PDF File</label>
                    <input type="file" name="file" class="form-control">
                    <div class="form-text">PDF, DOC, DOCX up to 50MB.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Cover Thumbnail Image</label>
                    <input type="file" name="thumbnail" class="form-control" accept="image/*">
                    <div class="form-text">JPG, PNG up to 5MB.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">External Reference Link</label>
                    <input type="url" name="reference_link" class="form-control" placeholder="https://..." value="{{ old('reference_link') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Price (&#8377;)</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', 0) }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="published" selected>Published</option>
                        <option value="draft">Draft</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>

                <div class="col-12">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="is_free" id="is_free" value="1" {{ old('is_free', true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_free">Free of Cost (Unrestricted download for all roles)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="is_copyright" id="is_copyright" value="1" {{ old('is_copyright', true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_copyright">Copyright Protected</label>
                    </div>
                </div>

                <div class="col-12 mt-4 pt-3 border-top d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin.resources.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-warning fw-bold px-4">
                        <i class="bi bi-save me-1"></i> Save & Upload Resource
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
