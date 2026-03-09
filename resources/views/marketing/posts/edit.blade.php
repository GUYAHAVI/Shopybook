@extends('layouts.dash')

@section('title', 'Edit Post')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0" style="color: var(--text-primary);">Edit Post</h1>
        <p class="text-muted mb-0">{{ Str::limit($post->title, 60) }}</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('marketing.posts.show', $post) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
        <a href="{{ route('marketing.posts.analytics', $post) }}" class="btn btn-outline-info">
            <i class="fas fa-chart-bar me-2"></i>Analytics
        </a>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-4">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>Please fix the following:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('marketing.posts.update', $post) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-4">

        <!-- Left: Main Fields -->
        <div class="col-lg-8">

            <!-- Title & Content -->
            <div class="card shadow-sm mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header py-3" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                    <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">
                        <i class="fas fa-file-alt me-2"></i>Post Content
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="title" class="form-label" style="color: var(--text-primary);">Title</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title"
                               value="{{ old('title', $post->title) }}"
                               placeholder="Enter a title for your post"
                               style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label" style="color: var(--text-primary);">Content</label>
                        <div class="position-relative">
                            <textarea class="form-control @error('content') is-invalid @enderror"
                                      id="content" name="content" rows="10"
                                      placeholder="Write your post content here..."
                                      style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">{{ old('content', $post->content) }}</textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-1 flex-wrap gap-2">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary" id="generateContentBtn" title="Generate new content with AI">
                                    <i class="fas fa-magic me-1"></i>Regenerate
                                </button>
                                <button type="button" class="btn btn-outline-success" id="enhanceContentBtn" title="Enhance existing content with AI">
                                    <i class="fas fa-wand-magic-sparkles me-1"></i>Enhance
                                </button>
                            </div>
                            <small id="charCount" class="text-muted">0 / 2200</small>
                        </div>
                        <div id="contentAiStatus" class="mt-2" style="display:none;">
                            <div class="alert alert-info py-2 mb-0">
                                <i class="fas fa-spinner fa-spin me-2"></i><small>AI is working…</small>
                            </div>
                        </div>
                        @error('content')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-0">
                        <label for="hashtags" class="form-label" style="color: var(--text-primary);">Hashtags</label>
                        <input type="text" class="form-control"
                               id="hashtags" name="hashtags"
                               value="{{ old('hashtags', is_array($post->hashtags) ? implode(' ', array_map(fn($t) => ltrim($t, '#'), $post->hashtags)) : $post->hashtags) }}"
                               placeholder="marketing business socialmedia (space-separated, no #)"
                               style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                        <small class="text-muted">Separate with spaces. The # will be added automatically.</small>
                    </div>
                </div>
            </div>

            <!-- Existing Media -->
            @if($post->media_files && is_array($post->media_files) && count($post->media_files) > 0)
            <div class="card shadow-sm mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header py-3" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                    <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">
                        <i class="fas fa-images me-2"></i>Current Media
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($post->media_files as $file)
                            @php
                                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                $isVideo = in_array($ext, ['mp4','mov','avi','webm']);
                                $relativePath = null;
                                if (str_contains($file, 'public/')) {
                                    $relativePath = 'storage/' . substr($file, strpos($file, 'public/') + 7);
                                }
                            @endphp
                            <div class="col-6 col-md-4 text-center">
                                @if($relativePath)
                                    @if($isVideo)
                                        <video class="w-100 rounded" style="max-height: 180px; object-fit: contain; background:#000;" controls>
                                            <source src="{{ asset($relativePath) }}">
                                        </video>
                                    @else
                                        <img src="{{ asset($relativePath) }}"
                                             style="display: block; max-width: 100%; width: auto; height: auto; max-height: 180px; margin: 0 auto; border-radius: 0.375rem;"
                                             alt="Post media"
                                             onerror="this.parentElement.innerHTML='<div class=\'text-muted p-3\'><i class=\'fas fa-image fa-2x\'></i><br><small>Image unavailable</small></div>'">
                                    @endif
                                @else
                                    <div class="text-center p-3 rounded" style="background: var(--bg-tertiary);">
                                        <i class="fas fa-{{ $isVideo ? 'video' : 'image' }} fa-2x text-muted"></i>
                                        <br><small class="text-muted">{{ basename($file) }}</small>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <p class="text-muted small mt-3 mb-0">
                        <i class="fas fa-info-circle me-1"></i>To change media, delete this post and create a new one.
                    </p>
                </div>
            </div>
            @endif

            <!-- AI Image Generation -->
            <div class="card shadow-sm mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header py-3" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                    <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">
                        <i class="fas fa-image me-2"></i>Regenerate AI Image
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="imagePrompt" class="form-label" style="color: var(--text-primary);">Image Prompt</label>
                        <textarea class="form-control" id="imagePrompt" rows="3"
                                  placeholder="Describe the image, or leave blank to auto-generate from the post content…"
                                  style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);"></textarea>
                        <div class="mt-2 d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="autoPromptBtn">
                                <i class="fas fa-wand-magic-sparkles me-1"></i>Auto from post
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="enhancePromptBtn">
                                <i class="fas fa-sparkles me-1"></i>Enhance prompt
                            </button>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label for="imageStyle" class="form-label" style="color: var(--text-primary);">Style</label>
                            <select class="form-select form-select-sm" id="imageStyle"
                                    style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                                <option value="realistic">Realistic Photo</option>
                                <option value="digital-art">Digital Art</option>
                                <option value="illustration">Illustration</option>
                                <option value="3d-render">3D Render</option>
                                <option value="minimalist">Minimalist</option>
                                <option value="vibrant">Vibrant &amp; Colorful</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="imageSize" class="form-label" style="color: var(--text-primary);">Size</label>
                            <select class="form-select form-select-sm" id="imageSize"
                                    style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                                <option value="1024x1024">Square (1024×1024)</option>
                                <option value="1792x1024">Landscape (1792×1024)</option>
                                <option value="1024x1792">Portrait (1024×1792)</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" id="generateImageBtn">
                        <i class="fas fa-magic me-2"></i>Generate Image
                    </button>

                    <!-- Progress -->
                    <div id="imageGenProgress" class="mt-3" style="display:none;">
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-striped progress-bar-animated w-100"></div>
                        </div>
                        <small class="text-muted">Generating image… this may take 10–30 seconds.</small>
                    </div>

                    <!-- Preview -->
                    <div id="imageGenPreview" class="mt-3" style="display:none;">
                        <img id="previewImage"
                             src=""
                             style="display:block; max-width:100%; width:auto; height:auto; max-height:400px; margin:0 auto; border-radius:0.375rem;"
                             alt="Generated image">
                        <!-- hidden fields so the form can carry the new image on save -->
                        <input type="hidden" name="generated_image_url"           id="genImgUrl">
                        <input type="hidden" name="generated_image_local_path"    id="genImgLocalPath">
                        <input type="hidden" name="generated_image_relative_path" id="genImgRelativePath">
                        <div class="mt-2 d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="regenImageBtn">
                                <i class="fas fa-redo me-1"></i>Regenerate
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-info" id="downloadImageBtn">
                                <i class="fas fa-download me-1"></i>Download
                            </button>
                        </div>
                        <p class="text-muted small mt-2 mb-0">
                            <i class="fas fa-info-circle me-1"></i>Save the post to attach this new image.
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right: Settings -->
        <div class="col-lg-4">

            <!-- Publish Settings -->
            <div class="card shadow-sm mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header py-3" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                    <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">
                        <i class="fas fa-cog me-2"></i>Publish Settings
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" style="color: var(--text-primary);">Post Type</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="post_type"
                                       id="typeImmediate" value="immediate"
                                       {{ old('post_type', $post->post_type) === 'immediate' ? 'checked' : '' }}
                                       onchange="toggleScheduled(this.value)">
                                <label class="form-check-label" for="typeImmediate" style="color: var(--text-primary);">
                                    Publish immediately
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="post_type"
                                       id="typeScheduled" value="scheduled"
                                       {{ old('post_type', $post->post_type) === 'scheduled' ? 'checked' : '' }}
                                       onchange="toggleScheduled(this.value)">
                                <label class="form-check-label" for="typeScheduled" style="color: var(--text-primary);">
                                    Schedule
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="scheduledAtField" style="display: {{ old('post_type', $post->post_type) === 'scheduled' ? 'block' : 'none' }};">
                        <label for="scheduled_at" class="form-label" style="color: var(--text-primary);">Scheduled Date &amp; Time</label>
                        <input type="datetime-local" class="form-control @error('scheduled_at') is-invalid @enderror"
                               id="scheduled_at" name="scheduled_at"
                               value="{{ old('scheduled_at', $post->scheduled_at ? $post->scheduled_at->format('Y-m-d\TH:i') : '') }}"
                               style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                        @error('scheduled_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Target Platforms -->
            <div class="card shadow-sm mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header py-3" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                    <h6 class="mb-0 fw-bold" style="color: var(--text-primary);">
                        <i class="fas fa-share-alt me-2"></i>Target Platforms
                    </h6>
                </div>
                <div class="card-body">
                    @if($connectedAccounts->count() > 0)
                        @php
                            $currentPlatforms = old('target_platforms', $post->target_platforms ?? []);
                            $platformIcons = [
                                'facebook'  => ['fab fa-facebook',  '#1877f2', 'Facebook'],
                                'instagram' => ['fab fa-instagram', '#e4405f', 'Instagram'],
                                'twitter'   => ['fab fa-x-twitter', '#000',    'X (Twitter)'],
                                'linkedin'  => ['fab fa-linkedin',  '#0077b5', 'LinkedIn'],
                                'telegram'  => ['fab fa-telegram',  '#0088cc', 'Telegram'],
                                'discord'   => ['fab fa-discord',   '#7289da', 'Discord'],
                            ];
                        @endphp
                        @foreach($connectedAccounts as $account)
                            @php [$ico, $col, $name] = $platformIcons[$account->platform] ?? ['fas fa-share-alt','#6c757d',ucfirst($account->platform)]; @endphp
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox"
                                       name="target_platforms[]"
                                       id="platform_{{ $account->platform }}"
                                       value="{{ $account->platform }}"
                                       {{ in_array($account->platform, $currentPlatforms) ? 'checked' : '' }}>
                                <label class="form-check-label" for="platform_{{ $account->platform }}" style="color: var(--text-primary);">
                                    <i class="{{ $ico }} me-2" style="color: {{ $col }};"></i>{{ $name }}
                                    <small class="text-muted d-block ms-4">{{ $account->username }}</small>
                                </label>
                            </div>
                        @endforeach
                        @error('target_platforms')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            No social media accounts connected.
                            <a href="{{ route('marketing.social-media') }}">Connect an account</a> first.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Save / Cancel -->
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Save Changes
                </button>
                <a href="{{ route('marketing.posts.show', $post) }}" class="btn btn-outline-secondary">
                    Cancel
                </a>
            </div>

        </div>
    </div>
</form>

<script>
// ── Shared helpers ────────────────────────────────────────────────────────────
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function showToast(message, isSuccess) {
    const existing = document.getElementById('editToast');
    if (existing) existing.remove();
    const colour = isSuccess ? '#198754' : '#dc3545';
    const icon   = isSuccess ? 'fa-check-circle' : 'fa-exclamation-circle';
    document.body.insertAdjacentHTML('beforeend', `
        <div id="editToast" style="
            position:fixed; bottom:1.5rem; right:1.5rem; z-index:11000;
            background:${colour}; color:#fff; padding:.75rem 1.25rem;
            border-radius:.5rem; box-shadow:0 4px 12px rgba(0,0,0,.25);
            display:flex; align-items:center; gap:.6rem; max-width:380px;
            animation:slideUp .25s ease-out;">
            <i class="fas ${icon}"></i><span>${message}</span>
        </div>
        <style>@keyframes slideUp{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}</style>
    `);
    setTimeout(() => { const t = document.getElementById('editToast'); if(t) t.remove(); }, 5000);
}

function post(url, body) {
    return fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(body)
    }).then(r => r.json());
}

// ── Character counter ─────────────────────────────────────────────────────────
const contentEl = document.getElementById('content');
const charCount  = document.getElementById('charCount');
function updateCount() {
    const len = contentEl.value.length;
    charCount.textContent = len + ' / 2200';
    charCount.style.color = len > 2000 ? '#dc3545' : '';
}
contentEl.addEventListener('input', updateCount);
updateCount();

// ── AI Content: Regenerate ────────────────────────────────────────────────────
document.getElementById('generateContentBtn').addEventListener('click', function () {
    const keywords = prompt('Enter keywords or topic to regenerate content (e.g. "summer sale, new arrivals"):');
    if (!keywords || !keywords.trim()) return;

    const status = document.getElementById('contentAiStatus');
    status.style.display = 'block';
    this.disabled = true;
    const btn = this;

    post('{{ route("marketing.posts.ai.generate") }}', {
        keywords,
        title:    document.getElementById('title').value,
        hashtags: document.getElementById('hashtags').value
    })
    .then(data => {
        status.style.display = 'none';
        btn.disabled = false;
        if (data.success) {
            contentEl.value = data.content;
            if (data.suggested_hashtags) document.getElementById('hashtags').value = data.suggested_hashtags;
            updateCount();
            showToast('Content regenerated!', true);
        } else {
            showToast(data.message || 'Failed to generate content.', false);
        }
    })
    .catch(() => { status.style.display='none'; btn.disabled=false; showToast('Network error.', false); });
});

// ── AI Content: Enhance ───────────────────────────────────────────────────────
document.getElementById('enhanceContentBtn').addEventListener('click', function () {
    const text = contentEl.value.trim();
    if (!text) { showToast('Write some content first before enhancing.', false); return; }

    const status = document.getElementById('contentAiStatus');
    status.style.display = 'block';
    this.disabled = true;
    const btn = this;

    post('{{ route("marketing.posts.ai.enhance") }}', { content: text })
    .then(data => {
        status.style.display = 'none';
        btn.disabled = false;
        if (data.success) {
            contentEl.value = data.enhanced_content;
            updateCount();
            showToast('Content enhanced!', true);
        } else {
            showToast(data.message || 'Failed to enhance content.', false);
        }
    })
    .catch(() => { status.style.display='none'; btn.disabled=false; showToast('Network error.', false); });
});

// ── AI Image helpers ──────────────────────────────────────────────────────────
function doGenerateImage() {
    const promptEl   = document.getElementById('imagePrompt');
    const progress   = document.getElementById('imageGenProgress');
    const preview    = document.getElementById('imageGenPreview');
    const genBtn     = document.getElementById('generateImageBtn');
    const regenBtn   = document.getElementById('regenImageBtn');

    let prompt = promptEl.value.trim();
    if (!prompt) {
        // auto-build from post content
        const title   = document.getElementById('title').value.trim();
        const content = contentEl.value.trim().substring(0, 200);
        prompt = (title ? `Image for: ${title}. ` : '') + `Visual for: ${content}`;
        promptEl.value = prompt;
    }

    progress.style.display = 'block';
    preview.style.display  = 'none';
    genBtn.disabled = true;
    if (regenBtn) regenBtn.disabled = true;

    post('{{ route("marketing.posts.ai.generate-image") }}', {
        prompt,
        style:        document.getElementById('imageStyle').value,
        size:         document.getElementById('imageSize').value,
        post_content: contentEl.value,
        post_title:   document.getElementById('title').value
    })
    .then(data => {
        progress.style.display = 'none';
        genBtn.disabled = false;
        if (regenBtn) regenBtn.disabled = false;

        if (data.success && data.image_url) {
            const img = document.getElementById('previewImage');
            img.onload = () => { preview.style.display = 'block'; showToast('Image generated!', true); };
            img.onerror = () => showToast('Image generated but could not be loaded.', false);
            img.src = data.image_url + '?t=' + Date.now();
            document.getElementById('genImgUrl').value          = data.image_url;
            document.getElementById('genImgLocalPath').value    = data.local_path    || '';
            document.getElementById('genImgRelativePath').value = data.relative_path || '';
        } else {
            showToast(data.message || 'Could not generate image. Try a different prompt.', false);
        }
    })
    .catch(() => {
        progress.style.display = 'none';
        genBtn.disabled = false;
        if (regenBtn) regenBtn.disabled = false;
        showToast('Network error generating image.', false);
    });
}

document.getElementById('generateImageBtn').addEventListener('click', doGenerateImage);
document.getElementById('regenImageBtn').addEventListener('click', doGenerateImage);

// ── Auto-build prompt from post ───────────────────────────────────────────────
document.getElementById('autoPromptBtn').addEventListener('click', function () {
    const title   = document.getElementById('title').value.trim();
    const content = contentEl.value.trim().substring(0, 200);
    if (!content) { showToast('Add some post content first.', false); return; }
    document.getElementById('imagePrompt').value =
        (title ? `Image for: ${title}. ` : '') + `Visual for: ${content}`;
    showToast('Prompt auto-filled from post!', true);
});

// ── Enhance image prompt ──────────────────────────────────────────────────────
document.getElementById('enhancePromptBtn').addEventListener('click', function () {
    const promptEl = document.getElementById('imagePrompt');
    if (!promptEl.value.trim()) { showToast('Enter a prompt first.', false); return; }
    this.disabled = true;
    const btn = this;

    post('{{ route("marketing.posts.ai.enhance-prompt") }}', {
        prompt:       promptEl.value,
        post_content: contentEl.value,
        style:        document.getElementById('imageStyle').value
    })
    .then(data => {
        btn.disabled = false;
        if (data.success && data.enhanced_prompt) {
            promptEl.value = data.enhanced_prompt;
            showToast('Prompt enhanced!', true);
        } else {
            showToast(data.message || 'Could not enhance prompt.', false);
        }
    })
    .catch(() => { btn.disabled = false; showToast('Network error.', false); });
});

// ── Download generated image ──────────────────────────────────────────────────
document.getElementById('downloadImageBtn').addEventListener('click', function () {
    const url = document.getElementById('genImgUrl').value;
    if (!url) return;
    fetch(url).then(r => r.blob()).then(blob => {
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'ai-image-' + Date.now() + '.png';
        a.click();
        URL.revokeObjectURL(a.href);
    }).catch(() => showToast('Could not download image.', false));
});

// ── Scheduled toggle ──────────────────────────────────────────────────────────
function toggleScheduled(value) {
    document.getElementById('scheduledAtField').style.display = value === 'scheduled' ? 'block' : 'none';
}
</script>
@endsection
