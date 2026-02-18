@extends('layouts.admin')

@section('title', __('accounting.title'))
@section('page-title', __('accounting.overview'))

@section('breadcrumb')
<li class="breadcrumb-item">
    <span class="bullet bg-gray-500 w-5px h-2px"></span>
</li>
<li class="breadcrumb-item text-muted">{{ __('accounting.breadcrumb') }}</li>
@endsection

@section('toolbar-actions')
<div class="d-flex gap-2">
    <button type="button" class="btn btn-sm btn-light-success" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
        <i class="ki-duotone ki-wallet fs-5 me-1">
            <span class="path1"></span>
            <span class="path2"></span>
            <span class="path3"></span>
            <span class="path4"></span>
        </i>
        {{ __('accounting.record_payment') }}
    </button>
    <button type="button" class="btn btn-sm btn-light-warning" data-bs-toggle="modal" data-bs-target="#recordCreditModal">
        <i class="ki-duotone ki-arrow-circle-left fs-5 me-1">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
        {{ __('accounting.issue_credit') }}
    </button>
    <div class="dropdown">
        <button type="button" class="btn btn-sm btn-light" data-bs-toggle="dropdown">
            <i class="ki-duotone ki-file-down fs-5 me-1">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            {{ __('accounting.export_csv') }}
        </button>
        <div class="dropdown-menu dropdown-menu-end">
            <a href="{{ route('admin.accounting.export', ['type' => 'all', 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" class="dropdown-item">{{ __('accounting.all_transactions') }}</a>
            <a href="{{ route('admin.accounting.export', ['type' => 'payments', 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" class="dropdown-item">{{ __('accounting.payments_only') }}</a>
            <a href="{{ route('admin.accounting.export', ['type' => 'bookings', 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" class="dropdown-item">{{ __('accounting.bookings_only') }}</a>
            <div class="dropdown-divider"></div>
            <a href="{{ route('admin.accounting.export-balances') }}" class="dropdown-item">{{ __('accounting.partner_balances_export') }}</a>
        </div>
    </div>
</div>
@endsection

@section('content')
@if(session('success'))
<div class="alert alert-success d-flex align-items-center mb-5">
    <i class="ki-duotone ki-check-circle fs-2hx text-success me-3">
        <span class="path1"></span>
        <span class="path2"></span>
    </i>
    <div class="d-flex flex-column">
        <span>{{ session('success') }}</span>
    </div>
</div>
@endif

<!--begin::Date Filter-->
<div class="card mb-5">
    <div class="card-body py-4">
        <form method="GET" action="{{ route('admin.accounting.index') }}" class="row g-3 align-items-end">
            <div class="col-auto">
                <label class="form-label">{{ __('accounting.date_type') }}</label>
                <select name="date_type" class="form-select form-select-solid w-150px">
                    <option value="booking_date" {{ $dateType === 'booking_date' ? 'selected' : '' }}>{{ __('accounting.booking_date') }}</option>
                    <option value="tour_date" {{ $dateType === 'tour_date' ? 'selected' : '' }}>{{ __('accounting.tour_date') }}</option>
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label">{{ __('accounting.from_date') }}</label>
                <input type="date" name="start_date" class="form-control form-control-solid" value="{{ $startDate->format('Y-m-d') }}">
            </div>
            <div class="col-auto">
                <label class="form-label">{{ __('accounting.to_date') }}</label>
                <input type="date" name="end_date" class="form-control form-control-solid" value="{{ $endDate->format('Y-m-d') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">{{ __('accounting.apply_filter') }}</button>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.accounting.index') }}" class="btn btn-light">{{ __('accounting.reset') }}</a>
            </div>
        </form>
    </div>
</div>
<!--end::Date Filter-->

<!--begin::Summary Cards-->
<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    <div class="col-md-6 col-lg-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center mb-3">
                    <div class="symbol symbol-50px me-3">
                        <span class="symbol-label bg-light-primary">
                            <i class="ki-duotone ki-chart-simple fs-2x text-primary">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                        </span>
                    </div>
                    <div>
                        <div class="fs-7 text-muted fw-semibold">{{ __('accounting.total_revenue') }}</div>
                        <div class="fs-7 text-muted">{{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }}</div>
                    </div>
                </div>
                <div class="fs-2hx fw-bold text-gray-900">{{ number_format($totalRevenue, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center mb-3">
                    <div class="symbol symbol-50px me-3">
                        <span class="symbol-label bg-light-success">
                            <i class="ki-duotone ki-wallet fs-2x text-success">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                        </span>
                    </div>
                    <div>
                        <div class="fs-7 text-muted fw-semibold">{{ __('accounting.payments_received') }}</div>
                        <div class="fs-7 text-muted">{{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }}</div>
                    </div>
                </div>
                <div class="fs-2hx fw-bold text-gray-900">{{ number_format($totalPayments, 2) }}</div>
                <div class="text-muted fs-7 fw-semibold">{{ $paymentCount }} {{ __('accounting.transactions') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center mb-3">
                    <div class="symbol symbol-50px me-3">
                        <span class="symbol-label bg-light-danger">
                            <i class="ki-duotone ki-bill fs-2x text-danger">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                                <span class="path6"></span>
                            </i>
                        </span>
                    </div>
                    <div>
                        <div class="fs-7 text-muted fw-semibold">{{ __('accounting.outstanding') }}</div>
                        <div class="fs-7 text-muted">{{ __('accounting.total') }}</div>
                    </div>
                </div>
                <div class="fs-2hx fw-bold text-danger">{{ number_format($totalOutstanding, 2) }}</div>
                <div class="text-muted fs-7 fw-semibold">{{ __('accounting.partners_with_balance', ['count' => $partnersWithBalance]) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center mb-3">
                    <div class="symbol symbol-50px me-3">
                        <span class="symbol-label bg-light-warning">
                            <i class="ki-duotone ki-shield-cross fs-2x text-warning">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                        </span>
                    </div>
                    <div>
                        <div class="fs-7 text-muted fw-semibold">{{ __('accounting.penalties') }}</div>
                        <div class="fs-7 text-muted">{{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }}</div>
                    </div>
                </div>
                <div class="fs-2hx fw-bold text-gray-900">{{ number_format($totalPenalties, 2) }}</div>
                <div class="text-muted fs-7 fw-semibold">{{ __('accounting.no_shows_late_cancellations', ['count' => $penaltyCount]) }}</div>
            </div>
        </div>
    </div>
</div>
<!--end::Summary Cards-->

<!--begin::Unpaid Bookings (Bulk Mark as Paid)-->
@if($unpaidBookingsCount > 0)
<div class="card mb-5 mb-xl-10">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="me-2">
                <i class="ki-duotone ki-document fs-2 text-warning">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </span>
            {{ __('accounting.unpaid_bookings') }}
            <span class="badge badge-light-warning ms-2">{{ $unpaidBookingsCount }}</span>
        </div>
        <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-success" id="bulkMarkPaidBtn" disabled onclick="openBulkPaymentModal()">
                <i class="ki-duotone ki-check-circle fs-5 me-1">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                {{ __('accounting.mark_selected_as_paid') }}
                <span id="selectedCount" class="badge badge-light-success ms-1 d-none">0</span>
            </button>
        </div>
    </div>
    <div class="card-body py-4">
        <form id="bulkPaymentForm" action="{{ route('admin.accounting.bulk-mark-paid') }}" method="POST">
            @csrf
            <input type="hidden" name="method" id="bulk_method" value="bank_transfer">
            <input type="hidden" name="paid_at" id="bulk_paid_at" value="{{ date('Y-m-d') }}">
            <input type="hidden" name="reference" id="bulk_reference" value="">
            <input type="hidden" name="notes" id="bulk_notes" value="">

            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-25px">
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" id="selectAllBookings" onclick="toggleAllBookings()">
                                </div>
                            </th>
                            <th>{{ __('accounting.booking') }}</th>
                            <th>{{ __('accounting.partner') }}</th>
                            <th>{{ __('accounting.tour') }}</th>
                            <th>{{ __('accounting.date') }}</th>
                            <th class="text-end">{{ __('accounting.amount') }}</th>
                            <th class="text-end">{{ __('accounting.paid') }}</th>
                            <th class="text-end">{{ __('accounting.balance') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        @foreach($unpaidBookings as $partnerId => $partnerBookings)
                            @php $partner = $partnerBookings->first()->partner; @endphp
                            <tr class="bg-light">
                                <td colspan="8">
                                    <div class="d-flex align-items-center py-2">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                            <input class="form-check-input partner-checkbox" type="checkbox" data-partner="{{ $partnerId }}" onclick="togglePartnerBookings({{ $partnerId }})">
                                        </div>
                                        <div class="symbol symbol-30px me-3">
                                            <span class="symbol-label bg-light-primary text-primary fw-bold">{{ $partner->initials }}</span>
                                        </div>
                                        <span class="fw-bold">{{ $partner->name }}</span>
                                        <span class="badge badge-light ms-2">{{ $partnerBookings->count() }} {{ __('accounting.bookings') }}</span>
                                        <span class="badge badge-light-danger ms-2">{{ number_format($partnerBookings->sum('balance_due'), 2) }} {{ __('accounting.due') }}</span>
                                    </div>
                                </td>
                            </tr>
                            @foreach($partnerBookings as $booking)
                            <tr>
                                <td>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                        <input class="form-check-input booking-checkbox" type="checkbox" name="booking_ids[]" value="{{ $booking->id }}" data-partner="{{ $partnerId }}" data-amount="{{ $booking->balance_due }}" onclick="updateSelectedCount()">
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="text-gray-800 text-hover-primary fw-bold">{{ $booking->booking_code }}</a>
                                </td>
                                <td>{{ $partner->name }}</td>
                                <td>{{ $booking->tourDeparture?->tour?->name ?? 'N/A' }}</td>
                                <td>{{ $booking->tourDeparture?->date?->format('M d, Y') ?? 'N/A' }}</td>
                                <td class="text-end">{{ number_format($booking->total_amount, 2) }}</td>
                                <td class="text-end text-success">{{ number_format($booking->amount_paid, 2) }}</td>
                                <td class="text-end text-danger fw-bold">{{ number_format($booking->balance_due, 2) }}</td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>
@endif
<!--end::Unpaid Bookings-->

<!--begin::Partner Balances-->
<div class="card mb-5 mb-xl-10">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="me-2">
                <i class="ki-duotone ki-people fs-2 text-primary">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                    <span class="path4"></span>
                    <span class="path5"></span>
                </i>
            </span>
            {{ __('accounting.partner_balances') }}
        </div>
        <div class="card-toolbar">
            <form method="GET" action="{{ route('admin.accounting.index') }}" class="d-flex align-items-center gap-2">
                @if(request('start_date'))
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                @endif
                @if(request('end_date'))
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                @endif
                <select name="balance_filter" class="form-select form-select-solid form-select-sm w-150px" onchange="this.form.submit()">
                    <option value="all" {{ $balanceFilter === 'all' ? 'selected' : '' }}>{{ __('accounting.all_partners') }}</option>
                    <option value="outstanding" {{ $balanceFilter === 'outstanding' ? 'selected' : '' }}>{{ __('accounting.with_balance') }}</option>
                    <option value="paid" {{ $balanceFilter === 'paid' ? 'selected' : '' }}>{{ __('accounting.fully_paid') }}</option>
                </select>
            </form>
        </div>
    </div>
    <div class="card-body py-4">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th>{{ __('accounting.partner') }}</th>
                        <th>{{ __('accounting.type') }}</th>
                        <th class="text-end">{{ __('accounting.total_billed') }}</th>
                        <th class="text-end">{{ __('accounting.paid') }}</th>
                        <th class="text-end">{{ __('accounting.outstanding') }}</th>
                        <th class="text-end">{{ __('accounting.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                    @forelse($partners as $partner)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40px me-3">
                                    <span class="symbol-label bg-light-primary text-primary fw-bold">{{ $partner->initials }}</span>
                                </div>
                                <div>
                                    <a href="{{ route('admin.partners.show', $partner) }}" class="text-gray-900 fw-bold text-hover-primary">{{ $partner->name }}</a>
                                    <div class="text-muted fs-7">{{ $partner->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge badge-light">{{ ucfirst($partner->type->value) }}</span></td>
                        <td class="text-end">{{ number_format($partner->total_billed, 2) }}</td>
                        <td class="text-end text-success">{{ number_format($partner->total_paid, 2) }}</td>
                        <td class="text-end {{ $partner->balance > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                            {{ number_format($partner->balance, 2) }}
                        </td>
                        <td class="text-end">
                            @if($partner->balance > 0)
                            <button type="button" class="btn btn-sm btn-light-success me-1"
                                    onclick="selectPartnerForPayment({{ $partner->id }}, '{{ $partner->name }}', {{ $partner->balance }})">
                                <i class="ki-duotone ki-wallet fs-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                                {{ __('accounting.pay') }}
                            </button>
                            @endif
                            <a href="{{ route('admin.partners.show', $partner) }}" class="btn btn-sm btn-light">{{ __('accounting.view') }}</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-10">
                            {{ __('accounting.no_partners_found') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<!--end::Partner Balances-->

<!--begin::Recent Transactions-->
<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="me-2">
                <i class="ki-duotone ki-arrow-mix fs-2 text-primary">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </span>
            {{ __('accounting.recent_transactions') }}
        </div>
    </div>
    <div class="card-body py-4">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th>{{ __('accounting.date') }}</th>
                        <th>{{ __('accounting.type') }}</th>
                        <th>{{ __('accounting.partner') }}</th>
                        <th>{{ __('accounting.description') }}</th>
                        <th>{{ __('accounting.method') }}</th>
                        <th class="text-end">{{ __('accounting.amount') }}</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                    @forelse($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction['date']->format('M d, Y') }}</td>
                        <td>
                            @php
                                $typeBadge = match($transaction['type']) {
                                    'payment' => 'badge-light-success',
                                    'booking' => 'badge-light-primary',
                                    'refund' => 'badge-light-warning',
                                    'penalty' => 'badge-light-danger',
                                    default => 'badge-light',
                                };
                            @endphp
                            <span class="badge {{ $typeBadge }}">{{ __('accounting.' . $transaction['type']) }}</span>
                        </td>
                        <td>{{ $transaction['partner']->name }}</td>
                        <td>
                            @if($transaction['booking_code'])
                                <a href="#" class="text-gray-800 text-hover-primary">{{ $transaction['description'] }}</a>
                            @else
                                {{ $transaction['description'] }}
                            @endif
                        </td>
                        <td>{{ $transaction['method'] ?? '-' }}</td>
                        <td class="text-end {{ $transaction['amount'] > 0 ? 'text-success' : 'text-danger' }} fw-bold">
                            {{ $transaction['amount'] > 0 ? '+' : '' }} {{ number_format($transaction['amount'], 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-10">
                            {{ __('accounting.no_transactions_for_period') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<!--end::Recent Transactions-->

<!--begin::Record Payment Modal-->
<div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.accounting.payment') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h2 class="fw-bold">{{ __('accounting.record_payment') }}</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="mb-5">
                        <label class="form-label required fw-semibold fs-6">{{ __('accounting.partner') }}</label>
                        <select name="partner_id" id="payment_partner_id" class="form-select form-select-solid" required>
                            <option value="">{{ __('accounting.select_partner') }}</option>
                            @foreach($partnersForDropdown as $partner)
                                <option value="{{ $partner['id'] }}" data-outstanding="{{ $partner['outstanding'] }}">
                                    {{ $partner['name'] }} ({{ number_format($partner['outstanding'], 2) }} {{ __('accounting.outstanding') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="form-label required fw-semibold fs-6">{{ __('accounting.payment_amount') }}</label>
                        <div class="input-group input-group-solid">
                            <span class="input-group-text">EUR</span>
                            <input type="number" name="amount" id="payment_amount" class="form-control form-control-solid" placeholder="0.00" step="0.01" min="0.01" required>
                        </div>
                    </div>
                    <div class="mb-5">
                        <label class="form-label required fw-semibold fs-6">{{ __('accounting.payment_method') }}</label>
                        <select name="method" class="form-select form-select-solid" required>
                            <option value="bank_transfer">{{ __('accounting.bank_transfer') }}</option>
                            <option value="cash">{{ __('accounting.cash') }}</option>
                            <option value="credit_card">{{ __('accounting.credit_card') }}</option>
                            <option value="check">{{ __('accounting.check') }}</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="form-label required fw-semibold fs-6">{{ __('accounting.payment_date') }}</label>
                        <input type="date" name="paid_at" class="form-control form-control-solid" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-5">
                        <label class="form-label fw-semibold fs-6">{{ __('accounting.reference_number') }}</label>
                        <input type="text" name="reference" class="form-control form-control-solid" placeholder="{{ __('accounting.reference_placeholder') }}">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold fs-6">{{ __('accounting.payment_notes') }}</label>
                        <textarea name="notes" class="form-control form-control-solid" rows="3" placeholder="{{ __('accounting.notes_placeholder') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('accounting.cancel') }}</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ki-duotone ki-check fs-4 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        {{ __('accounting.record_payment') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Record Payment Modal-->

<!--begin::Record Credit Modal-->
<div class="modal fade" id="recordCreditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.accounting.credit') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h2 class="fw-bold">{{ __('accounting.issue_credit_refund') }}</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-5">
                        <i class="ki-duotone ki-information fs-2 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        {{ __('accounting.credit_info') }}
                    </div>
                    <div class="mb-5">
                        <label class="form-label required fw-semibold fs-6">{{ __('accounting.partner') }}</label>
                        <select name="partner_id" class="form-select form-select-solid" required>
                            <option value="">{{ __('accounting.select_partner') }}</option>
                            @foreach($partnersForDropdown as $partner)
                                <option value="{{ $partner['id'] }}">{{ $partner['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="form-label required fw-semibold fs-6">{{ __('accounting.credit_amount') }}</label>
                        <div class="input-group input-group-solid">
                            <span class="input-group-text">EUR</span>
                            <input type="number" name="amount" class="form-control form-control-solid" placeholder="0.00" step="0.01" min="0.01" required>
                        </div>
                    </div>
                    <div class="mb-5">
                        <label class="form-label required fw-semibold fs-6">{{ __('accounting.credit_reason') }}</label>
                        <select name="reason" class="form-select form-select-solid" required>
                            <option value="">{{ __('accounting.select_reason') }}</option>
                            <option value="bad_weather">{{ __('accounting.bad_weather') }}</option>
                            <option value="tour_cancelled">{{ __('accounting.tour_cancelled') }}</option>
                            <option value="other">{{ __('accounting.other') }}</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="form-label fw-semibold fs-6">{{ __('accounting.related_booking') }}</label>
                        <input type="text" name="booking_code" class="form-control form-control-solid" placeholder="{{ __('accounting.related_booking_placeholder') }}">
                    </div>
                    <div class="mb-5">
                        <label class="form-label required fw-semibold fs-6">{{ __('accounting.date') }}</label>
                        <input type="date" name="date" class="form-control form-control-solid" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold fs-6">{{ __('accounting.additional_notes') }}</label>
                        <textarea name="notes" class="form-control form-control-solid" rows="2" placeholder="{{ __('accounting.additional_details_placeholder') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('accounting.cancel') }}</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="ki-duotone ki-check fs-4 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        {{ __('accounting.issue_credit') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Record Credit Modal-->

<!--begin::Bulk Mark as Paid Modal-->
<div class="modal fade" id="bulkMarkPaidModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">{{ __('accounting.confirm_bulk_payment') }}</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-5">
                    <i class="ki-duotone ki-information fs-2 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    {!! __('accounting.bulk_payment_info', ['count' => '<span id="modalBookingCount">0</span>']) !!}
                    <br>{{ __('accounting.total_amount') }}: <strong id="modalTotalAmount">0.00</strong>
                </div>
                <div class="mb-5">
                    <label class="form-label required fw-semibold fs-6">{{ __('accounting.payment_method') }}</label>
                    <select id="modalMethod" class="form-select form-select-solid" required>
                        <option value="bank_transfer">{{ __('accounting.bank_transfer') }}</option>
                        <option value="cash">{{ __('accounting.cash') }}</option>
                        <option value="credit_card">{{ __('accounting.credit_card') }}</option>
                        <option value="check">{{ __('accounting.check') }}</option>
                    </select>
                </div>
                <div class="mb-5">
                    <label class="form-label required fw-semibold fs-6">{{ __('accounting.payment_date') }}</label>
                    <input type="date" id="modalPaidAt" class="form-control form-control-solid" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="mb-5">
                    <label class="form-label fw-semibold fs-6">{{ __('accounting.reference_number') }}</label>
                    <input type="text" id="modalReference" class="form-control form-control-solid" placeholder="{{ __('accounting.reference_placeholder') }}">
                </div>
                <div class="mb-0">
                    <label class="form-label fw-semibold fs-6">{{ __('accounting.payment_notes') }}</label>
                    <textarea id="modalNotes" class="form-control form-control-solid" rows="2" placeholder="{{ __('accounting.notes_placeholder') }}"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('accounting.cancel') }}</button>
                <button type="button" class="btn btn-success" onclick="submitBulkPayment()">
                    <i class="ki-duotone ki-check fs-4 me-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    {{ __('accounting.confirm_payment') }}
                </button>
            </div>
        </div>
    </div>
</div>
<!--end::Bulk Mark as Paid Modal-->
@endsection

@push('scripts')
<script>
    function selectPartnerForPayment(partnerId, partnerName, outstanding) {
        document.getElementById('payment_partner_id').value = partnerId;
        document.getElementById('payment_amount').value = outstanding.toFixed(2);
        var modal = new bootstrap.Modal(document.getElementById('recordPaymentModal'));
        modal.show();
    }

    // Bulk payment functionality
    function toggleAllBookings() {
        var selectAll = document.getElementById('selectAllBookings');
        var checkboxes = document.querySelectorAll('.booking-checkbox');
        var partnerCheckboxes = document.querySelectorAll('.partner-checkbox');

        checkboxes.forEach(function(checkbox) {
            checkbox.checked = selectAll.checked;
        });

        partnerCheckboxes.forEach(function(checkbox) {
            checkbox.checked = selectAll.checked;
        });

        updateSelectedCount();
    }

    function togglePartnerBookings(partnerId) {
        var partnerCheckbox = document.querySelector('.partner-checkbox[data-partner="' + partnerId + '"]');
        var bookingCheckboxes = document.querySelectorAll('.booking-checkbox[data-partner="' + partnerId + '"]');

        bookingCheckboxes.forEach(function(checkbox) {
            checkbox.checked = partnerCheckbox.checked;
        });

        updateSelectedCount();
    }

    function updateSelectedCount() {
        var checkboxes = document.querySelectorAll('.booking-checkbox:checked');
        var count = checkboxes.length;
        var totalAmount = 0;

        checkboxes.forEach(function(checkbox) {
            totalAmount += parseFloat(checkbox.dataset.amount) || 0;
        });

        var countBadge = document.getElementById('selectedCount');
        var bulkBtn = document.getElementById('bulkMarkPaidBtn');

        if (count > 0) {
            countBadge.textContent = count;
            countBadge.classList.remove('d-none');
            bulkBtn.disabled = false;
        } else {
            countBadge.classList.add('d-none');
            bulkBtn.disabled = true;
        }

        // Update partner checkbox states
        var partnerIds = [...new Set([...document.querySelectorAll('.booking-checkbox')].map(cb => cb.dataset.partner))];
        partnerIds.forEach(function(partnerId) {
            var partnerCheckbox = document.querySelector('.partner-checkbox[data-partner="' + partnerId + '"]');
            var bookingCheckboxes = document.querySelectorAll('.booking-checkbox[data-partner="' + partnerId + '"]');
            var checkedBookings = document.querySelectorAll('.booking-checkbox[data-partner="' + partnerId + '"]:checked');

            if (partnerCheckbox) {
                partnerCheckbox.checked = checkedBookings.length === bookingCheckboxes.length && bookingCheckboxes.length > 0;
                partnerCheckbox.indeterminate = checkedBookings.length > 0 && checkedBookings.length < bookingCheckboxes.length;
            }
        });

        // Update select all checkbox state
        var allCheckboxes = document.querySelectorAll('.booking-checkbox');
        var selectAll = document.getElementById('selectAllBookings');
        if (selectAll) {
            selectAll.checked = checkboxes.length === allCheckboxes.length && allCheckboxes.length > 0;
            selectAll.indeterminate = checkboxes.length > 0 && checkboxes.length < allCheckboxes.length;
        }
    }

    function openBulkPaymentModal() {
        var checkboxes = document.querySelectorAll('.booking-checkbox:checked');
        var count = checkboxes.length;
        var totalAmount = 0;

        checkboxes.forEach(function(checkbox) {
            totalAmount += parseFloat(checkbox.dataset.amount) || 0;
        });

        document.getElementById('modalBookingCount').textContent = count;
        document.getElementById('modalTotalAmount').textContent = '€' + totalAmount.toFixed(2);

        var modal = new bootstrap.Modal(document.getElementById('bulkMarkPaidModal'));
        modal.show();
    }

    function submitBulkPayment() {
        // Transfer modal values to form hidden inputs
        document.getElementById('bulk_method').value = document.getElementById('modalMethod').value;
        document.getElementById('bulk_paid_at').value = document.getElementById('modalPaidAt').value;
        document.getElementById('bulk_reference').value = document.getElementById('modalReference').value;
        document.getElementById('bulk_notes').value = document.getElementById('modalNotes').value;

        // Submit the form
        document.getElementById('bulkPaymentForm').submit();
    }
</script>
@endpush
