<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>{{ $title ?? 'Report' }}</title>
<style>
    @page { margin: 110px 42px 55px; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 10.5px; color: #2d2d2d; }
    header { position: fixed; top: -95px; left: 0; right: 0; height: 95px; border-bottom: 2px solid #2c3e50; padding-bottom: 8px; }
    footer { position: fixed; bottom: -40px; left: 0; right: 0; text-align: center; font-size: 8.5px; color: #999; border-top: 1px solid #ddd; padding-top: 5px; }
    .pagenum:before { content: counter(page) " / " counter(pages); }
    .h-left { float: left; } .h-right { float: right; text-align: right; }
    .h-logo { max-height: 62px; max-width: 160px; }
    .h-biz { font-size: 15px; font-weight: bold; color: #2c3e50; }
    .h-sub { font-size: 9px; color: #777; line-height: 1.5; }
    .h-title { font-size: 16px; font-weight: bold; color: #2c3e50; }
    .h-meta { font-size: 9px; color: #777; }
    table.data { width: 100%; border-collapse: collapse; margin-top: 6px; }
    table.data th { background: #2c3e50; color: #fff; padding: 6px 8px; font-size: 9.5px; text-align: left; }
    table.data td { padding: 5px 8px; border-bottom: 1px solid #e8e8e8; }
    table.data tr:nth-child(even) td { background: #f7f9fa; }
    .num { text-align: right; }
    .totals td { font-weight: bold; background: #eef2f5 !important; border-top: 2px solid #2c3e50; }
    .sum-row { margin-top: 12px; }
    .sum-table { width: 100%; }
    .sum-table td { padding: 4px 10px; border: 1px solid #ddd; font-size: 10px; }
    .sum-label { color: #777; font-size: 8.5px; text-transform: uppercase; letter-spacing: .5px; }
    .sum-val { font-weight: bold; font-size: 12px; }
    .ok { color: #1a7a3c; } .warn { color: #b8860b; } .bad { color: #b02318; }
</style>
</head>
<body>
<header>
    <div class="h-left">
        @if(!empty($logo))
            <img src="{{ $logo }}" class="h-logo"><br>
        @endif
        <div class="h-biz">{{ $business->name }}</div>
        <div class="h-sub">
            {{ collect([$business->phone, $business->email, $business->address])->filter()->implode(' · ') }}
        </div>
    </div>
    <div class="h-right">
        <div class="h-title">{{ $title ?? 'Report' }}</div>
        <div class="h-meta">{{ $subtitle ?? '' }}</div>
        <div class="h-meta">Generated {{ now()->format('d M Y, H:i') }}</div>
    </div>
    <div style="clear: both;"></div>
</header>
<footer>
    {{ $business->name }} · {{ $title ?? 'Report' }} · Page <span class="pagenum"></span>
</footer>

@yield('report-content')
</body>
</html>
