@php
    /**
     * Silent honeypot + timing fields for bot protection.
     * Include <x-honeypot /> inside any <form> to enable protection.
     * The middleware 'honeypot' must be applied to the route.
     */
@endphp
<div style="position:absolute;left:-9999px;top:-9999px;width:1px;height:1px;overflow:hidden;" aria-hidden="true">
    <label for="website_url_{{ uniqid() }}">Website (leave empty)</label>
    <input type="text" name="website_url" id="website_url_{{ uniqid() }}" value="" tabindex="-1" autocomplete="off" aria-hidden="true">
    <input type="hidden" name="form_started_at" value="{{ now()->toISOString() }}">
</div>
