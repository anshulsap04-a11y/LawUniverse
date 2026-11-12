@extends(auth()->user()->isSuperAdmin() ? 'layouts.admin' : 'layouts.user-portal', ['title' => 'Ask AI Chatbot - Law Universe'])

@section('content')
<div class="row g-4">
    <!-- Conversations Sidebar -->
    <div class="col-md-4 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Chat History</h6>
                <a href="{{ route('chatbot.ask') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg"></i> New
                </a>
            </div>
            <div class="list-group list-group-flush" style="max-height: 520px; overflow-y: auto;">
                @forelse($conversations as $c)
                    <a href="{{ route('chatbot.ask', ['conversation_id' => $c->id]) }}" class="list-group-item list-group-item-action py-3 {{ ($activeConversation && $activeConversation->id === $c->id) ? 'active bg-primary-subtle border-primary text-primary' : '' }}">
                        <div class="fw-semibold text-truncate">{{ $c->title }}</div>
                        <small class="text-muted">{{ $c->created_at->diffForHumans() }}</small>
                    </a>
                @empty
                    <div class="p-3 text-center text-muted small">No previous chats.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Active Chat Window -->
    <div class="col-md-8 col-lg-9">
        <div class="card border-0 shadow-sm rounded-3 d-flex flex-column" style="min-height: 580px;">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-robot text-primary me-2"></i> Legal AI Assistant</h5>
                    <small class="text-muted">Powered by Law Universe statutory & judicial citation repository</small>
                </div>
                @if(auth()->user()->isSuperAdmin())
                    <a href="{{ route('admin.ai.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-cpu me-1"></i> GPT Engine Settings
                    </a>
                @endif
            </div>

            <div class="card-body p-4 flex-grow-1 overflow-auto" id="chatMessages" style="max-height: 480px;">
                @if($activeConversation && $activeConversation->messages->count() > 0)
                    @foreach($activeConversation->messages as $msg)
                        <div class="d-flex mb-3 {{ $msg->role === 'user' ? 'justify-content-end' : 'justify-content-start' }}">
                            <div class="p-3 rounded-3 {{ $msg->role === 'user' ? 'bg-primary text-white' : 'bg-light text-dark border' }}" style="max-width: 80%;">
                                <div class="small fw-bold mb-1 opacity-75">{{ $msg->role === 'user' ? 'You' : 'Legal AI' }}</div>
                                <div style="white-space: pre-line;">{!! nl2br(e($msg->content)) !!}</div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5 text-muted">
                        <div class="fs-1 text-primary mb-2"><i class="bi bi-chat-quote"></i></div>
                        <h5>How can I assist your legal research today?</h5>
                        <p class="small">Ask questions regarding Bare Acts, Constitutional Articles, Legal Maxims, or Landmark Precedents.</p>
                        
                        <div class="d-flex flex-wrap justify-content-center gap-2 mt-3">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="setPrompt('Explain Article 21 and Right to Privacy precedents')">Article 21 & Privacy</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="setPrompt('What is the punishment for murder under BNS 2023?')">BNS Section 103 Murder</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="setPrompt('Explain the legal maxim Audi Alteram Partem')">Audi Alteram Partem</button>
                        </div>
                    </div>
                @endif
            </div>

            <div class="card-footer bg-white p-3 border-top">
                <form action="{{ route('chatbot.submit') }}" method="POST" id="promptForm">
                    @csrf
                    @if($activeConversation)
                        <input type="hidden" name="conversation_id" value="{{ $activeConversation->id }}">
                    @endif
                    <div class="input-group">
                        <input type="text" name="prompt" id="promptInput" class="form-control py-3 ps-3" required placeholder="Type your legal query here..." autocomplete="off">
                        <button type="submit" class="btn btn-warning fw-bold px-4">
                            <i class="bi bi-send-fill me-1"></i> Ask AI
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function setPrompt(text) {
    document.getElementById('promptInput').value = text;
    document.getElementById('promptForm').submit();
}

window.addEventListener('DOMContentLoaded', () => {
    const chatContainer = document.getElementById('chatMessages');
    if (chatContainer) chatContainer.scrollTop = chatContainer.scrollHeight;
});
</script>
@endsection
