{{-- Report page header: title + optional date filters. Vars: $title, $blurb, $asOf (single date) or $startDate/$endDate. --}}
<div class="card mb-4">
    <div class="card-header pb-0">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5>{{ $title }}</h5>
                <p class="text-sm mb-0">{{ $blurb ?? '' }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('accounting.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Financial Reports</a>
                <button class="btn btn-sm btn-success" onclick="window.print()"><i class="fas fa-print me-2"></i>Print</button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            @isset($asOf)
                <div class="col-md-6">
                    <label class="form-label text-sm">As of</label>
                    <input type="date" class="form-control" name="as_of" value="{{ $asOf }}">
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            @else
                <div class="col-md-5">
                    <label class="form-label text-sm">From</label>
                    <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                </div>
                <div class="col-md-5">
                    <label class="form-label text-sm">To</label>
                    <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            @endif
        </form>
    </div>
</div>
