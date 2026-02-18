@extends('layouts.admin')

@section('title', __('reports.title'))
@section('page-title', __('reports.analytics_dashboard'))

@section('breadcrumb')
<li class="breadcrumb-item">
    <i class="ki-duotone ki-right fs-7 text-gray-700 mx-n1"></i>
</li>
<li class="breadcrumb-item text-gray-500">{{ __('reports.title') }}</li>
@endsection

@section('toolbar-actions')
<form method="GET" action="{{ route('admin.reports.index') }}" class="d-flex gap-2">
    <select name="period" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
        <option value="week" {{ $period === 'week' ? 'selected' : '' }}>{{ __('reports.last_week') }}</option>
        <option value="month" {{ $period === 'month' ? 'selected' : '' }}>{{ __('reports.last_month') }}</option>
        <option value="quarter" {{ $period === 'quarter' ? 'selected' : '' }}>{{ __('reports.last_quarter') }}</option>
        <option value="year" {{ $period === 'year' ? 'selected' : '' }}>{{ __('reports.last_year') }}</option>
        <option value="all" {{ $period === 'all' ? 'selected' : '' }}>{{ __('reports.all_time') }}</option>
    </select>
</form>
@endsection

@section('content')
<!--begin::Stats Row-->
<div class="row g-5 g-xl-8 mb-5 mb-xl-8">
    <!--begin::Col-->
    <div class="col-xl-3">
        <div class="card card-xl">
            <div class="card-body d-flex align-items-center justify-content-between p-6">
                <div>
                    <span class="text-gray-800 fw-bolder d-block fs-2">{{ number_format($stats['confirmed_bookings']) }}</span>
                    <span class="text-gray-500 fw-semibold fs-7">{{ __('reports.confirmed_bookings') }}</span>
                </div>
                <span class="symbol symbol-50px">
                    <span class="symbol-label bg-light-primary">
                        <i class="ki-duotone ki-calendar-tick fs-2x text-primary">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                            <span class="path6"></span>
                        </i>
                    </span>
                </span>
            </div>
        </div>
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-xl-3">
        <div class="card card-xl">
            <div class="card-body d-flex align-items-center justify-content-between p-6">
                <div>
                    <span class="text-gray-800 fw-bolder d-block fs-2">{{ number_format($stats['total_passengers']) }}</span>
                    <span class="text-gray-500 fw-semibold fs-7">{{ __('reports.total_passengers') }}</span>
                </div>
                <span class="symbol symbol-50px">
                    <span class="symbol-label bg-light-info">
                        <i class="ki-duotone ki-people fs-2x text-info">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                        </i>
                    </span>
                </span>
            </div>
        </div>
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-xl-3">
        <div class="card card-xl">
            <div class="card-body d-flex align-items-center justify-content-between p-6">
                <div>
                    <span class="text-gray-800 fw-bolder d-block fs-2">{{ number_format($stats['total_revenue'], 2) }}</span>
                    <span class="text-gray-500 fw-semibold fs-7">{{ __('reports.total_revenue') }}</span>
                </div>
                <span class="symbol symbol-50px">
                    <span class="symbol-label bg-light-success">
                        <i class="ki-duotone ki-dollar fs-2x text-success">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                    </span>
                </span>
            </div>
        </div>
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-xl-3">
        <div class="card card-xl">
            <div class="card-body d-flex align-items-center justify-content-between p-6">
                <div>
                    <span class="text-gray-800 fw-bolder d-block fs-2">{{ number_format($stats['active_partners']) }}</span>
                    <span class="text-gray-500 fw-semibold fs-7">{{ __('reports.active_partners') }}</span>
                </div>
                <span class="symbol symbol-50px">
                    <span class="symbol-label bg-light-warning">
                        <i class="ki-duotone ki-briefcase fs-2x text-warning">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </span>
                </span>
            </div>
        </div>
    </div>
    <!--end::Col-->
</div>
<!--end::Stats Row-->

<!--begin::Revenue & Payments Row-->
<div class="row g-5 g-xl-8 mb-5 mb-xl-8">
    <div class="col-xl-4">
        <div class="card card-xl">
            <div class="card-body d-flex align-items-center justify-content-between p-6">
                <div>
                    <span class="text-gray-800 fw-bolder d-block fs-2">{{ number_format($stats['paid_amount'], 2) }}</span>
                    <span class="text-gray-500 fw-semibold fs-7">{{ __('reports.paid_amount') }}</span>
                </div>
                <span class="symbol symbol-50px">
                    <span class="symbol-label bg-light-success">
                        <i class="ki-duotone ki-check-circle fs-2x text-success">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </span>
                </span>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card card-xl">
            <div class="card-body d-flex align-items-center justify-content-between p-6">
                <div>
                    <span class="text-gray-800 fw-bolder d-block fs-2">{{ number_format($stats['outstanding_amount'], 2) }}</span>
                    <span class="text-gray-500 fw-semibold fs-7">{{ __('reports.outstanding_amount') }}</span>
                </div>
                <span class="symbol symbol-50px">
                    <span class="symbol-label bg-light-danger">
                        <i class="ki-duotone ki-time fs-2x text-danger">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </span>
                </span>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card card-xl">
            <div class="card-body d-flex align-items-center justify-content-between p-6">
                <div>
                    <span class="text-gray-800 fw-bolder d-block fs-2">{{ number_format($stats['avg_booking_value'], 2) }}</span>
                    <span class="text-gray-500 fw-semibold fs-7">{{ __('reports.avg_booking_value') }}</span>
                </div>
                <span class="symbol symbol-50px">
                    <span class="symbol-label bg-light-primary">
                        <i class="ki-duotone ki-graph-up fs-2x text-primary">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                            <span class="path6"></span>
                        </i>
                    </span>
                </span>
            </div>
        </div>
    </div>
</div>
<!--end::Revenue & Payments Row-->

<!--begin::Content Row-->
<div class="row g-5 g-xl-8">
    <!--begin::Revenue by Tour-->
    <div class="col-xl-6">
        <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">{{ __('reports.revenue_by_tour') }}</span>
                    <span class="text-muted fw-semibold fs-7">{{ __('reports.top_performing_tours') }}</span>
                </h3>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4">
                        <thead>
                            <tr class="border-0 text-muted fw-bold fs-7 text-uppercase">
                                <th>{{ __('reports.tour') }}</th>
                                <th class="text-end">{{ __('reports.bookings') }}</th>
                                <th class="text-end">{{ __('reports.revenue') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($revenueByTour as $tour)
                            <tr>
                                <td>
                                    <span class="text-gray-800 fw-bold d-block fs-6">{{ $tour->name }}</span>
                                    <span class="text-muted fw-semibold d-block fs-7">{{ $tour->code }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="text-gray-800 fw-bold">{{ $tour->bookings_count }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="text-gray-800 fw-bold">{{ number_format($tour->total_revenue, 2) }}</span>
                                    @if($tour->total_revenue > $tour->paid_revenue)
                                        <span class="text-danger fs-8 d-block">{{ number_format($tour->total_revenue - $tour->paid_revenue, 2) }} {{ __('reports.unpaid') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-5">{{ __('reports.no_data') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!--end::Revenue by Tour-->

    <!--begin::Top Partners-->
    <div class="col-xl-6">
        <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">{{ __('reports.top_partners') }}</span>
                    <span class="text-muted fw-semibold fs-7">{{ __('reports.by_booking_count') }}</span>
                </h3>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4">
                        <thead>
                            <tr class="border-0 text-muted fw-bold fs-7 text-uppercase">
                                <th>{{ __('reports.partner') }}</th>
                                <th class="text-end">{{ __('reports.bookings') }}</th>
                                <th class="text-end">{{ __('reports.revenue') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topPartners as $partner)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.partners.show', $partner->id) }}" class="text-gray-800 text-hover-primary fw-bold d-block fs-6">{{ $partner->name }}</a>
                                    <span class="text-muted fw-semibold d-block fs-7">{{ $partner->type instanceof \App\Enums\PartnerType ? $partner->type->label() : ucfirst(str_replace('_', ' ', $partner->type ?? '')) }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="text-gray-800 fw-bold">{{ $partner->bookings_count }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="text-gray-800 fw-bold">{{ number_format($partner->total_revenue, 2) }}</span>
                                    @if($partner->total_revenue > $partner->paid_amount)
                                        <span class="text-danger fs-8 d-block">{{ number_format($partner->total_revenue - $partner->paid_amount, 2) }} {{ __('reports.unpaid') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-5">{{ __('reports.no_data') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!--end::Top Partners-->
</div>
<!--end::Content Row-->

<!--begin::Upcoming Capacity-->
<div class="row g-5 g-xl-8">
    <div class="col-12">
        <div class="card card-xl-stretch mb-5 mb-xl-8">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">{{ __('reports.upcoming_capacity') }}</span>
                    <span class="text-muted fw-semibold fs-7">{{ __('reports.next_7_days') }}</span>
                </h3>
            </div>
            <div class="card-body py-3">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4">
                        <thead>
                            <tr class="border-0 text-muted fw-bold fs-7 text-uppercase">
                                <th>{{ __('reports.tour') }}</th>
                                <th>{{ __('reports.date') }}</th>
                                <th>{{ __('reports.time') }}</th>
                                <th class="text-center">{{ __('reports.capacity') }}</th>
                                <th class="text-center">{{ __('reports.booked') }}</th>
                                <th class="text-center">{{ __('reports.available') }}</th>
                                <th>{{ __('reports.utilization') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($upcomingCapacity as $departure)
                            <tr>
                                <td>
                                    <span class="text-gray-800 fw-bold d-block fs-6">{{ $departure['tour'] }}</span>
                                    <span class="text-muted fw-semibold d-block fs-7">{{ $departure['tour_code'] }}</span>
                                </td>
                                <td class="text-gray-700">{{ $departure['date'] }}</td>
                                <td class="text-gray-700">{{ $departure['time'] }}</td>
                                <td class="text-center">{{ $departure['capacity'] }}</td>
                                <td class="text-center fw-bold">{{ $departure['booked'] }}</td>
                                <td class="text-center">{{ $departure['remaining'] }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress h-8px w-100 me-2" style="max-width: 100px;">
                                            @php
                                                $color = $departure['utilization'] >= 80 ? 'success' : ($departure['utilization'] >= 50 ? 'warning' : 'danger');
                                            @endphp
                                            <div class="progress-bar bg-{{ $color }}" role="progressbar" style="width: {{ $departure['utilization'] }}%"></div>
                                        </div>
                                        <span class="text-gray-700 fw-semibold">{{ $departure['utilization'] }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">{{ __('reports.no_upcoming_departures') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Upcoming Capacity-->

<!--begin::Bookings Trend Chart-->
<div class="row g-5 g-xl-8">
    <div class="col-12">
        <div class="card card-xl-stretch">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">{{ __('reports.bookings_trend') }}</span>
                    <span class="text-muted fw-semibold fs-7">{{ __('reports.last_30_days') }}</span>
                </h3>
            </div>
            <div class="card-body">
                <div id="bookings_chart" style="height: 300px;"></div>
            </div>
        </div>
    </div>
</div>
<!--end::Bookings Trend Chart-->
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const bookingsData = @json($bookingsTrend);
    const dates = Object.keys(bookingsData);
    const counts = Object.values(bookingsData);

    var options = {
        series: [{
            name: '{{ __('reports.bookings') }}',
            data: counts
        }],
        chart: {
            type: 'area',
            height: 300,
            toolbar: {
                show: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        xaxis: {
            type: 'datetime',
            categories: dates,
            labels: {
                datetimeFormatter: {
                    day: 'dd MMM'
                }
            }
        },
        yaxis: {
            min: 0,
            forceNiceScale: true,
            labels: {
                formatter: function(val) {
                    return Math.floor(val);
                }
            }
        },
        tooltip: {
            x: {
                format: 'dd MMM yyyy'
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.2,
                stops: [0, 90, 100]
            }
        },
        colors: ['#009ef7']
    };

    var chart = new ApexCharts(document.querySelector("#bookings_chart"), options);
    chart.render();
});
</script>
@endpush
