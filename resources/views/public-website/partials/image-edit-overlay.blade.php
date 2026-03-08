{{--
    Image edit overlay — shown on hover in preview mode.
    Variables: $section (WebsiteSection), $imageQuery (string|null)
--}}
<div class="section-img-edit-overlay">
    <button class="img-edit-btn ai-btn"
            onclick="openImageModal({{ $section->id }}, '{{ addslashes($imageQuery ?? '') }}'); event.stopPropagation();">
        <i class="fas fa-magic"></i> AI Image
    </button>
    <label class="img-edit-btn" style="cursor:pointer;" onclick="event.stopPropagation();">
        <i class="fas fa-upload"></i> Upload
        <input type="file" accept="image/*" style="display:none;"
               onchange="uploadSectionImage({{ $section->id }}, this)">
    </label>
</div>
