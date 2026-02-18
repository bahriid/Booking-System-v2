@extends('layouts.driver')

@section('title', __('driver.my_shifts'))
@section('page-title', __('driver.my_shifts'))

@section('toolbar-actions')
<div class="d-flex gap-2">
    <form method="GET" action="{{ route('driver.dashboard') }}">
        <input type="date"
               class="form-control form-control-solid w-auto"
               name="date"
               value="{{ $selectedDate->format('Y-m-d') }}"
               onchange="this.form.submit()">
    </form>
</div>
@endsection

@section('content')
<!--begin::Selected Date Shifts-->
<div class="card mb-5 mb-xl-10">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="me-2">
                <i class="ki-duotone ki-calendar-8 fs-2 text-primary">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                    <span class="path4"></span>
                    <span class="path5"></span>
                    <span class="path6"></span>
                </i>
            </span>
            @if($selectedDate->isToday())
                {{ __('driver.todays_shifts') }}
            @elseif($selectedDate->isTomorrow())
                {{ __('driver.tomorrows_shifts') }}
            @elseif($selectedDate->isYesterday())
                {{ __('driver.yesterdays_shifts') }}
            @else
                {{ __('driver.shifts_for', ['day' => $selectedDate->format('l')]) }}
            @endif
            <span class="badge badge-light-primary ms-2">{{ $selectedDate->format('F d, Y') }}</span>
        </div>
    </div>
    <div class="card-body py-4">
        @if($todaysShifts->isEmpty())
            <div class="text-center py-10">
                <i class="ki-duotone ki-calendar-remove fs-3tx text-gray-400 mb-5">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                    <span class="path4"></span>
                </i>
                <div class="text-muted fs-5">{{ __('driver.no_shifts_for_date') }}</div>
            </div>
        @else
        <div class="row g-5">
            @foreach($todaysShifts as $shift)
            @php
                $colorVariant = $loop->even ? 'info' : 'primary';
            @endphp
            <div class="col-md-6">
                <div class="card border border-gray-300 border-dashed rounded">
                    <div class="card-header border-0 pt-5">
                        <div class="card-title">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-50px me-4">
                                    <span class="symbol-label bg-light-{{ $colorVariant }}">
                                        <i class="ki-duotone ki-map fs-2x text-{{ $colorVariant }}">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </span>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fs-4 fw-bold">{{ ($shift->tour?->name ?? 'N/A') }}</span>
                                    <span class="text-muted fs-7">{{ ($shift->tour?->code ?? '-') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-toolbar">
                            <span class="badge badge-light-success fs-7">{{ $shift->time }}</span>
                        </div>
                    </div>
                    <div class="card-body py-4">
                        <div class="d-flex flex-stack mb-4">
                            <span class="text-muted fw-semibold">{{ __('driver.total_passengers') }}</span>
                            <span class="fw-bold fs-4">{{ $shift->total_passengers }} pax</span>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            @if(isset($shift->pax_counts['adult']) && $shift->pax_counts['adult'] > 0)
                                <span class="badge badge-light-primary">{{ $shift->pax_counts['adult'] }} ADU</span>
                            @endif
                            @if(isset($shift->pax_counts['child']) && $shift->pax_counts['child'] > 0)
                                <span class="badge badge-light-info">{{ $shift->pax_counts['child'] }} CHD</span>
                            @endif
                            @if(isset($shift->pax_counts['infant']) && $shift->pax_counts['infant'] > 0)
                                <span class="badge badge-light-secondary">{{ $shift->pax_counts['infant'] }} INF</span>
                            @endif
                            @if($shift->total_passengers === 0)
                                <span class="text-muted">{{ __('driver.no_passengers_yet') }}</span>
                            @endif
                        </div>
                        <div class="separator my-4"></div>
                        <h6 class="fw-bold text-gray-800 mb-3">{{ __('driver.pickup_points') }}</h6>
                        <div class="d-flex flex-column gap-2 mb-4">
                            @forelse($shift->pickup_summary as $pickupName => $count)
                            <div class="d-flex flex-stack">
                                <span class="text-muted">{{ $pickupName }}</span>
                                <span class="badge badge-light">{{ $count }} pax</span>
                            </div>
                            @empty
                            <div class="text-muted">{{ __('driver.no_pickups') }}</div>
                            @endforelse
                        </div>
                        <div class="d-grid">
                            <button type="button"
                                    class="btn btn-primary btn-manifest"
                                    data-departure-id="{{ $shift->id }}"
                                    data-tour-name="{{ ($shift->tour?->name ?? 'N/A') }}">
                                <i class="ki-duotone ki-document fs-4 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                {{ __('driver.view_manifest') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4">
            {{ $todaysShifts->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
<!--end::Selected Date Shifts-->

<!--begin::Upcoming Shifts-->
<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="me-2">
                <i class="ki-duotone ki-time fs-2 text-primary">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </span>
            {{ __('driver.upcoming_shifts') }}
        </div>
    </div>
    <div class="card-body py-4">
        @if($upcomingShifts->isEmpty())
            <div class="text-center py-10">
                <div class="text-muted">{{ __('driver.no_upcoming_shifts_7_days') }}</div>
            </div>
        @else
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th>{{ __('driver.date') }}</th>
                        <th>{{ __('driver.time') }}</th>
                        <th>{{ __('driver.tour') }}</th>
                        <th>{{ __('driver.passengers') }}</th>
                        <th class="text-end">{{ __('driver.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                    @foreach($upcomingShifts as $shift)
                    <tr>
                        <td>
                            @if($shift->date->isTomorrow())
                                <span class="fw-bold">{{ __('driver.tomorrow') }}</span>
                            @else
                                <span class="fw-bold">{{ $shift->date->format('l') }}</span>
                            @endif
                            <div class="text-muted fs-7">{{ $shift->date->format('M d') }}</div>
                        </td>
                        <td>{{ $shift->time }}</td>
                        <td>
                            <span class="fw-bold">{{ ($shift->tour?->name ?? 'N/A') }}</span>
                            <div class="text-muted fs-7">{{ ($shift->tour?->code ?? '-') }}</div>
                        </td>
                        <td>
                            @if(isset($shift->pax_counts['adult']) && $shift->pax_counts['adult'] > 0)
                                <span class="badge badge-light-primary">{{ $shift->pax_counts['adult'] }} ADU</span>
                            @endif
                            @if(isset($shift->pax_counts['child']) && $shift->pax_counts['child'] > 0)
                                <span class="badge badge-light-info">{{ $shift->pax_counts['child'] }} CHD</span>
                            @endif
                            @if(isset($shift->pax_counts['infant']) && $shift->pax_counts['infant'] > 0)
                                <span class="badge badge-light-secondary">{{ $shift->pax_counts['infant'] }} INF</span>
                            @endif
                            @if($shift->total_passengers === 0)
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <button type="button"
                                    class="btn btn-sm btn-light btn-active-light-primary btn-manifest"
                                    data-departure-id="{{ $shift->id }}"
                                    data-tour-name="{{ ($shift->tour?->name ?? 'N/A') }}">
                                <i class="ki-duotone ki-document fs-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                {{ __('driver.manifest') }}
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $upcomingShifts->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
<!--end::Upcoming Shifts-->

<!--begin::Manifest Modal-->
<div class="modal fade" id="manifestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold" id="manifestModalTitle">{{ __('driver.tour_manifest') }}</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body" id="manifestModalBody">
                <div class="d-flex justify-content-center py-10">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">{{ __('driver.loading') }}</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('driver.close') }}</button>
                <a href="#" id="printManifestBtn" class="btn btn-primary">
                    <i class="ki-duotone ki-printer fs-4 me-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                        <span class="path5"></span>
                    </i>
                    {{ __('driver.download_pdf') }}
                </a>
            </div>
        </div>
    </div>
</div>
<!--end::Manifest Modal-->
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const manifestModal = new bootstrap.Modal(document.getElementById('manifestModal'));
        const modalTitle = document.getElementById('manifestModalTitle');
        const modalBody = document.getElementById('manifestModalBody');
        const printBtn = document.getElementById('printManifestBtn');

        // Handle manifest button clicks
        document.querySelectorAll('.btn-manifest').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const departureId = this.dataset.departureId;
                const tourName = this.dataset.tourName;

                // Show modal with loading state
                modalTitle.textContent = '{{ __('driver.tour_manifest') }} - ' + tourName;
                modalBody.innerHTML = `
                    <div class="d-flex justify-content-center py-10">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">{{ __('driver.loading') }}</span>
                        </div>
                    </div>
                `;

                // Set the PDF download URL
                printBtn.href = `{{ url('driver/departures') }}/${departureId}/manifest/pdf`;

                manifestModal.show();

                // Fetch manifest content
                fetch(`{{ url('driver/departures') }}/${departureId}/manifest`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to load manifest');
                        }
                        return response.text();
                    })
                    .then(html => {
                        modalBody.innerHTML = html;
                    })
                    .catch(error => {
                        modalBody.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="ki-duotone ki-cross-circle fs-2x text-danger me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                {{ __('driver.failed_to_load_manifest') }}
                            </div>
                        `;
                    });
            });
        });
    });
</script>
@endpush
