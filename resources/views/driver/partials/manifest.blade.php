<div class="d-flex flex-stack mb-5">
    <div>
        <div class="fs-5 fw-bold text-gray-800">{{ $departure->date->format('d/m/Y') }} at {{ $departure->time }}</div>
        <div class="text-muted">{{ $departure->tour?->code ?? '-' }}</div>
    </div>
    <div class="text-end">
        <div class="fs-4 fw-bold text-primary">{{ $passengers->count() }} {{ __('driver.passengers') }}</div>
        <div class="text-muted">
            @if(isset($paxCounts['adult'])){{ $paxCounts['adult'] }} ADU@endif
            @if(isset($paxCounts['child'])), {{ $paxCounts['child'] }} CHD@endif
            @if(isset($paxCounts['infant'])), {{ $paxCounts['infant'] }} INF@endif
        </div>
    </div>
</div>

<div class="separator my-5"></div>

<!--begin::Pickup Summary-->
<h5 class="fw-bold text-gray-800 mb-4">{{ __('driver.pickup_summary') }}</h5>
<div class="d-flex flex-column gap-2 mb-5">
    @foreach($pickupSummary as $pickupName => $data)
    <div class="d-flex flex-stack">
        <span class="text-muted">{{ $pickupName }} ({{ $data['time'] }})</span>
        <span class="badge badge-light">{{ $data['count'] }} pax</span>
    </div>
    @endforeach
</div>

<div class="separator my-5"></div>

<h5 class="fw-bold text-gray-800 mb-4">{{ __('driver.passenger_list') }}</h5>
<div class="table-responsive">
    <table class="table align-middle table-row-dashed fs-6 gy-4">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>#</th>
                <th>{{ __('driver.name') }}</th>
                <th>{{ __('driver.type') }}</th>
                <th>{{ __('driver.pickup') }}</th>
                <th>{{ __('driver.phone') }}</th>
                <th>{{ __('driver.allergies') }}</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @foreach($passengers as $index => $passenger)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="fw-bold">
                    {{ $passenger->first_name }} {{ $passenger->last_name }}
                    <div class="text-muted fs-7">{{ $passenger->booking_code ?? '-' }} - {{ $passenger->partner_name ?? '-' }}</div>
                </td>
                <td>
                    @php
                        $paxValue = $passenger->pax_type?->value ?? 'adult';
                        $badgeClass = match($paxValue) {
                            'adult' => 'badge-light-primary',
                            'child' => 'badge-light-info',
                            'infant' => 'badge-light-secondary',
                            default => 'badge-light',
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $passenger->pax_type?->shortCode() ?? 'ADU' }}</span>
                </td>
                <td>
                    @if($passenger->pickupPoint)
                        {{ $passenger->pickupPoint->name }}
                        <div class="text-muted fs-7">{{ $passenger->pickupPoint->default_time }}</div>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>{{ $passenger->phone ?: '-' }}</td>
                <td>
                    @if($passenger->allergies)
                        <span class="text-danger fw-bold">{{ $passenger->allergies }}</span>
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($allergiesCount > 0)
<div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4 mt-5">
    <i class="ki-duotone ki-information-2 fs-2tx text-warning me-3">
        <span class="path1"></span>
        <span class="path2"></span>
        <span class="path3"></span>
    </i>
    <div class="d-flex flex-stack flex-grow-1">
        <div class="fw-semibold">
            <div class="fs-6 text-gray-800 fw-bold">{{ __('driver.allergies_alert') }}</div>
            <div class="fs-7 text-gray-700">{{ __('driver.allergies_warning', ['count' => $allergiesCount]) }}</div>
        </div>
    </div>
</div>
@endif

@if($departure->notes)
<div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4 mt-5">
    <i class="ki-duotone ki-notepad fs-2tx text-info me-3">
        <span class="path1"></span>
        <span class="path2"></span>
        <span class="path3"></span>
        <span class="path4"></span>
        <span class="path5"></span>
    </i>
    <div class="d-flex flex-stack flex-grow-1">
        <div class="fw-semibold">
            <div class="fs-6 text-gray-800 fw-bold">{{ __('driver.notes') }}</div>
            <div class="fs-7 text-gray-700">{{ $departure->notes }}</div>
        </div>
    </div>
</div>
@endif
