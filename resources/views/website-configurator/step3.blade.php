@extends('layouts.dash')

@section('title', 'Choose Style & Fonts - Step 3')

@section('content')
<style>
    :root { --pp: #8b5cf6; --db: #1e293b; --ddb: #0f172a; }
    body { background: var(--ddb); color: #e2e8f0; }
    .cfg { max-width: 960px; margin: 0 auto; padding: 3rem 1.5rem; }
    /* progress */
    .prog { display:flex; justify-content:center; align-items:center; gap:1rem; margin-bottom:3.5rem; }
    .prog-step { display:flex; align-items:center; gap:.5rem; }
    .prog-circle { width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.9rem; }
    .prog-circle.done { background:#10b981;color:#fff; }
    .prog-circle.curr { background:var(--pp);color:#fff;box-shadow:0 0 20px rgba(139,92,246,.5); }
    .prog-circle.pend { background:var(--db);color:#64748b;border:2px solid #475569; }
    .prog-label { font-size:.85rem;color:#94a3b8; }
    .prog-label.curr { color:#fff;font-weight:600; }
    .prog-line { width:60px;height:2px;background:#475569; }
    .prog-line.done { background:#10b981; }
    /* alerts */
    .alert { padding:1rem 1.5rem;border-radius:8px;margin-bottom:1.5rem;display:flex;align-items:center;gap:.75rem;font-size:.95rem; }
    .alert-danger { background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#ef4444; }
    /* sections */
    .sec-head { display:flex;align-items:center;gap:.75rem;margin-bottom:1.25rem; }
    .sec-icon { width:26px;height:26px;color:var(--pp); }
    .sec-title { font-size:1.35rem;font-weight:700;color:#fff; }
    /* palette grid */
    .palette-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1rem;margin-bottom:2.5rem; }
    .palette-card { border:2px solid #334155;border-radius:12px;overflow:hidden;cursor:pointer;transition:all .25s;background:var(--db); }
    .palette-card:hover { border-color:var(--pp);transform:translateY(-3px); }
    .palette-card.active { border-color:var(--pp);box-shadow:0 0 0 3px rgba(139,92,246,.25); }
    .palette-swatches { height:54px;display:flex; }
    .swatch { flex:1; }
    .palette-name { padding:.6rem 1rem;font-size:.875rem;font-weight:600;color:#e2e8f0;display:flex;justify-content:space-between;align-items:center; }
    .palette-radio { display:none; }
    .palette-check { width:18px;height:18px;border-radius:50%;background:var(--pp);display:none;align-items:center;justify-content:center; }
    .palette-card.active .palette-check { display:flex; }
    /* custom colors */
    .custom-row { display:flex;flex-wrap:wrap;gap:1.5rem;margin-bottom:2.5rem; }
    .color-field { display:flex;flex-direction:column;gap:.35rem; }
    .color-field label { font-size:.82rem;color:#94a3b8;font-weight:500; }
    .color-wrap { display:flex;align-items:center;gap:.6rem; }
    .color-wrap input[type=color] { width:44px;height:44px;border:none;border-radius:8px;cursor:pointer;background:none;padding:2px; }
    .color-hex { width:90px;background:#334155;border:1px solid #475569;border-radius:6px;padding:.45rem .6rem;color:#e2e8f0;font-size:.85rem;font-family:monospace; }
    /* fonts */
    .font-grid { display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:2.5rem; }
    @media(max-width:600px){ .font-grid{ grid-template-columns:1fr; } }
    .font-field label { display:block;font-size:.9rem;color:#94a3b8;font-weight:500;margin-bottom:.5rem; }
    .font-select { width:100%;background:#1e293b;border:2px solid #334155;border-radius:8px;padding:.75rem 1rem;color:#e2e8f0;font-size:.95rem;cursor:pointer;transition:border-color .2s; }
    .font-select:focus { outline:none;border-color:var(--pp); }
    /* preview strip */
    .preview-strip { border-radius:12px;overflow:hidden;margin-bottom:2.5rem;border:1px solid #334155; }
    .preview-nav { height:44px;display:flex;align-items:center;gap:1rem;padding:0 1.5rem;font-size:.8rem;font-weight:600;letter-spacing:.5px; }
    .preview-body { padding:2rem; }
    .preview-h { font-size:1.6rem;font-weight:700;margin-bottom:.6rem; }
    .preview-p { font-size:.9rem;line-height:1.7;margin-bottom:1rem; }
    .preview-btn { display:inline-block;padding:.5rem 1.2rem;border-radius:6px;font-size:.85rem;font-weight:600; }
    /* action */
    .actions { display:flex;justify-content:space-between;align-items:center;margin-top:1.5rem; }
    .btn-back { background:transparent;color:#94a3b8;border:2px solid #475569;border-radius:8px;padding:.85rem 2rem;font-weight:600;font-size:1rem;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:.5rem;transition:all .2s; }
    .btn-back:hover { color:#fff;border-color:#64748b; }
    .btn-next { background:var(--pp);color:#fff;border:none;border-radius:8px;padding:.85rem 2.25rem;font-weight:700;font-size:1rem;cursor:pointer;display:inline-flex;align-items:center;gap:.5rem;transition:all .2s; }
    .btn-next:hover { background:#7c3aed;transform:translateY(-2px);box-shadow:0 10px 25px rgba(139,92,246,.35); }
    .tip { background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.3);border-radius:10px;padding:1.1rem 1.25rem;margin-top:2rem;font-size:.875rem;color:#bfdbfe; }
</style>

<!-- Google Fonts preloader — updated dynamically by JS -->
<link id="gfontLink" rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&family=Open+Sans&display=swap">

<div class="cfg">

    @if($errors->any())
    <div class="alert alert-danger">
        <span>⚠</span>
        <div><strong>Please fix the errors below:</strong>
            <ul style="margin:.5rem 0 0 1.5rem;padding:0">
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- Progress -->
    <div class="prog">
        <div class="prog-step"><div class="prog-circle done">✓</div><span class="prog-label">Type</span></div>
        <div class="prog-line done"></div>
        <div class="prog-step"><div class="prog-circle done">✓</div><span class="prog-label">Details</span></div>
        <div class="prog-line done"></div>
        <div class="prog-step"><div class="prog-circle curr">3</div><span class="prog-label curr">Style</span></div>
        <div class="prog-line"></div>
        <div class="prog-step"><div class="prog-circle pend">4</div><span class="prog-label">Build</span></div>
    </div>

    <!-- Header -->
    <div style="text-align:center;margin-bottom:2.5rem">
        <h1 style="font-size:2.2rem;font-weight:300;color:#fff;margin-bottom:.4rem">Choose Your Style</h1>
        <p style="color:#94a3b8;font-size:1.05rem">Pick a color palette and fonts — your AI website will use them throughout</p>
    </div>

    <form id="styleForm" method="POST" action="{{ route('website-configurator.step3.submit') }}">
        @csrf

        <!-- ── Color Schemes ─────────────────────────────────── -->
        <div class="sec-head">
            <svg class="sec-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
            <h2 class="sec-title">Color Scheme</h2>
        </div>

        @php
        $palettes = [
            ['id'=>'ocean',     'name'=>'Ocean Blue',       'primary'=>'#1565c0','secondary'=>'#00acc1','accent'=>'#ff7043'],
            ['id'=>'forest',    'name'=>'Forest Green',     'primary'=>'#2e7d32','secondary'=>'#558b2f','accent'=>'#ffa000'],
            ['id'=>'crimson',   'name'=>'Crimson & Gold',   'primary'=>'#c62828','secondary'=>'#ad1457','accent'=>'#f9a825'],
            ['id'=>'royal',     'name'=>'Royal Purple',     'primary'=>'#4527a0','secondary'=>'#6a1b9a','accent'=>'#f57c00'],
            ['id'=>'midnight',  'name'=>'Midnight Dark',    'primary'=>'#1a237e','secondary'=>'#283593','accent'=>'#00bcd4'],
            ['id'=>'sunset',    'name'=>'Sunset Orange',    'primary'=>'#e65100','secondary'=>'#bf360c','accent'=>'#fdd835'],
            ['id'=>'teal',      'name'=>'Teal & Coral',     'primary'=>'#00695c','secondary'=>'#00796b','accent'=>'#f44336'],
            ['id'=>'slate',     'name'=>'Slate & Amber',    'primary'=>'#37474f','secondary'=>'#455a64','accent'=>'#ffb300'],
        ];
        $defaultPalette = 'ocean';
        @endphp

        <div class="palette-grid">
            @foreach($palettes as $p)
            <div class="palette-card {{ $p['id'] === $defaultPalette ? 'active' : '' }}"
                 onclick="selectPalette('{{ $p['id'] }}','{{ $p['primary'] }}','{{ $p['secondary'] }}','{{ $p['accent'] }}')"
                 title="{{ $p['name'] }}">
                <input class="palette-radio" type="radio" name="_palette" id="pal_{{ $p['id'] }}" value="{{ $p['id'] }}"
                       {{ $p['id'] === $defaultPalette ? 'checked' : '' }}>
                <div class="palette-swatches">
                    <div class="swatch" style="background:{{ $p['primary'] }}"></div>
                    <div class="swatch" style="background:{{ $p['secondary'] }}"></div>
                    <div class="swatch" style="background:{{ $p['accent'] }}"></div>
                </div>
                <div class="palette-name">
                    {{ $p['name'] }}
                    <span class="palette-check">
                        <svg width="10" height="10" fill="none" stroke="#fff" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </span>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Custom colors -->
        <div style="margin-bottom:.75rem">
            <span style="font-size:.85rem;color:#64748b;font-weight:500;">Or fine-tune with custom colors:</span>
        </div>
        <div class="custom-row">
            <div class="color-field">
                <label for="cp_primary">Primary Color</label>
                <div class="color-wrap">
                    <input type="color" id="cp_primary" value="#1565c0" oninput="syncHex('primary',this.value)">
                    <input class="color-hex" type="text" id="hex_primary" value="#1565c0" maxlength="7"
                           oninput="syncColor('primary',this.value)" placeholder="#1565c0">
                </div>
            </div>
            <div class="color-field">
                <label for="cp_secondary">Secondary Color</label>
                <div class="color-wrap">
                    <input type="color" id="cp_secondary" value="#00acc1" oninput="syncHex('secondary',this.value)">
                    <input class="color-hex" type="text" id="hex_secondary" value="#00acc1" maxlength="7"
                           oninput="syncColor('secondary',this.value)" placeholder="#00acc1">
                </div>
            </div>
            <div class="color-field">
                <label for="cp_accent">Accent Color</label>
                <div class="color-wrap">
                    <input type="color" id="cp_accent" value="#ff7043" oninput="syncHex('accent',this.value)">
                    <input class="color-hex" type="text" id="hex_accent" value="#ff7043" maxlength="7"
                           oninput="syncColor('accent',this.value)" placeholder="#ff7043">
                </div>
            </div>
        </div>

        <!-- Hidden color inputs submitted with form -->
        <input type="hidden" name="primary_color"   id="val_primary"   value="#1565c0">
        <input type="hidden" name="secondary_color" id="val_secondary" value="#00acc1">
        <input type="hidden" name="accent_color"    id="val_accent"    value="#ff7043">

        <!-- ── Fonts ─────────────────────────────────────────── -->
        <div class="sec-head" style="margin-top:1rem">
            <svg class="sec-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <h2 class="sec-title">Typography</h2>
        </div>

        @php
        $googleFonts = [
            'Poppins','Inter','Raleway','Lato','Montserrat','Open Sans','Nunito',
            'Roboto','Work Sans','Josefin Sans','Outfit','DM Sans','Space Grotesk',
            'Ubuntu','Source Sans 3','Quicksand','Jost','Barlow','Lexend',
            'Playfair Display','Merriweather','Lora','Cormorant Garamond','EB Garamond',
        ];
        @endphp

        <div class="font-grid">
            <div class="font-field">
                <label for="heading_font">Heading Font</label>
                <select id="heading_font" name="heading_font" class="font-select" onchange="updateFontPreview()">
                    @foreach($googleFonts as $font)
                    <option value="{{ $font }}" {{ $font === 'Poppins' ? 'selected' : '' }}>{{ $font }}</option>
                    @endforeach
                </select>
            </div>
            <div class="font-field">
                <label for="body_font">Body Font</label>
                <select id="body_font" name="body_font" class="font-select" onchange="updateFontPreview()">
                    @foreach($googleFonts as $font)
                    <option value="{{ $font }}" {{ $font === 'Open Sans' ? 'selected' : '' }}>{{ $font }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- ── Live Preview ───────────────────────────────────── -->
        <div class="sec-head" style="margin-top:.5rem">
            <svg class="sec-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            <h2 class="sec-title">Live Preview</h2>
        </div>

        <div class="preview-strip" id="previewStrip">
            <div class="preview-nav" id="prevNav" style="background:#1565c0;color:#fff;">
                <strong id="prevBrand" style="font-family:'Poppins',sans-serif">Your Business</strong>
                <span style="opacity:.7">Home &nbsp; About &nbsp; Services &nbsp; Contact</span>
            </div>
            <div class="preview-body" id="prevBody" style="background:#ffffff">
                <div class="preview-h" id="prevHeading" style="font-family:'Poppins',sans-serif;color:#1565c0">
                    Building Something Great
                </div>
                <p class="preview-p" id="prevPara" style="font-family:'Open Sans',sans-serif;color:#333">
                    Your AI-generated website will look stunning with this style. Professional content is created automatically for your business.
                </p>
                <span class="preview-btn" id="prevBtn" style="background:#1565c0;color:#fff;font-family:'Poppins',sans-serif">
                    Get Started
                </span>
                &nbsp;
                <span class="preview-btn" id="prevBtn2" style="background:transparent;border:2px solid #1565c0;color:#1565c0;font-family:'Poppins',sans-serif">
                    Learn More
                </span>
            </div>
        </div>

        <!-- ── Buttons ────────────────────────────────────────── -->
        <div class="actions">
            <a href="{{ route('website-configurator.step2') }}" class="btn-back">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back
            </a>
            <button type="submit" class="btn-next">
                Continue to Build
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>

        <div class="tip">
            💡 <strong>Tip:</strong> You can always update your colors and fonts from the website dashboard after the AI builds your site.
        </div>
    </form>
</div>

<script>
// ── Palette logic ────────────────────────────────────────────
function selectPalette(id, primary, secondary, accent) {
    document.querySelectorAll('.palette-card').forEach(c => c.classList.remove('active'));
    document.getElementById('pal_' + id).checked = true;
    const card = document.querySelector('.palette-card[onclick*="' + id + '"]');
    if (card) card.classList.add('active');

    // Update pickers and hex fields
    setColor('primary',   primary);
    setColor('secondary', secondary);
    setColor('accent',    accent);

    updatePreview();
}

function setColor(key, hex) {
    document.getElementById('cp_'  + key).value  = hex;
    document.getElementById('hex_' + key).value  = hex;
    document.getElementById('val_' + key).value  = hex;
}

function syncHex(key, hex) {
    document.getElementById('hex_' + key).value = hex;
    document.getElementById('val_' + key).value  = hex;
    updatePreview();
}

function syncColor(key, hex) {
    if (/^#[0-9A-Fa-f]{6}$/.test(hex)) {
        document.getElementById('cp_'  + key).value = hex;
        document.getElementById('val_' + key).value  = hex;
        updatePreview();
    }
}

// ── Font preview logic ────────────────────────────────────────
function updateFontPreview() {
    const hf = document.getElementById('heading_font').value;
    const bf = document.getElementById('body_font').value;

    // Build Google Fonts URL for both fonts
    const families = [...new Set([hf, bf])].map(f => 'family=' + f.replace(/ /g, '+') + ':wght@400;700').join('&');
    document.getElementById('gfontLink').href = 'https://fonts.googleapis.com/css2?' + families + '&display=swap';

    updatePreview(hf, bf);
}

// ── Preview refresh ───────────────────────────────────────────
function updatePreview(hf, bf) {
    hf = hf || document.getElementById('heading_font').value;
    bf = bf || document.getElementById('body_font').value;

    const primary   = document.getElementById('val_primary').value   || '#1565c0';
    const secondary = document.getElementById('val_secondary').value || '#00acc1';

    document.getElementById('prevNav').style.background     = primary;
    document.getElementById('prevBrand').style.fontFamily   = "'" + hf + "',sans-serif";
    document.getElementById('prevHeading').style.color      = primary;
    document.getElementById('prevHeading').style.fontFamily = "'" + hf + "',sans-serif";
    document.getElementById('prevPara').style.fontFamily    = "'" + bf + "',sans-serif";
    document.getElementById('prevBtn').style.background     = primary;
    document.getElementById('prevBtn2').style.borderColor   = primary;
    document.getElementById('prevBtn2').style.color         = primary;
    document.getElementById('prevBtn').style.fontFamily     = "'" + hf + "',sans-serif";
    document.getElementById('prevBtn2').style.fontFamily    = "'" + hf + "',sans-serif";
}

// Init on load
document.addEventListener('DOMContentLoaded', function () {
    updatePreview();
});
</script>
@endsection
