@extends('layouts.admin')

@section('title', isset($partner) ? __('partners.edit_partner') : __('partners.new_partner'))
@section('page-title', isset($partner) ? __('partners.edit_partner') : __('partners.new_partner'))

@section('breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('admin.partners.index') }}" class="text-muted text-hover-primary">{{ __('partners.title') }}</a>
</li>
<li class="breadcrumb-item">
    <span class="bullet bg-gray-500 w-5px h-2px"></span>
</li>
<li class="breadcrumb-item text-muted">{{ isset($partner) ? __('partners.edit') : __('partners.new') }}</li>
@endsection

@section('content')
@if ($errors->any())
    <x-ui.alert type="danger" dismissible class="mb-5">
        <strong>{{ __('partners.please_fix_errors') }}</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-ui.alert>
@endif

<form action="{{ isset($partner) ? route('admin.partners.update', $partner) : route('admin.partners.store') }}" method="POST">
    @csrf
    @if (isset($partner))
        @method('PUT')
    @endif

    <div class="row g-5 g-xl-10">
        <!--begin::Main Column-->
        <div class="col-xl-8">
            <!--begin::Company Info Card-->
            <div class="card mb-5 mb-xl-10">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <span class="me-2">
                            <i class="ki-duotone ki-building fs-2 text-primary">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                        </span>
                        {{ __('partners.company_information') }}
                    </div>
                </div>
                <div class="card-body py-4">
                    <div class="row g-4">
                        <div class="col-md-8">
                            <x-forms.input
                                name="name"
                                :label="__('partners.company_name')"
                                :value="old('name', $partner->name ?? '')"
                                placeholder="e.g. Hotel Bella Vista"
                                required
                            />
                        </div>
                        <div class="col-md-4">
                            <x-forms.select
                                name="type"
                                :label="__('partners.type')"
                                :value="old('type', isset($partner) ? $partner->type->value : '')"
                                required
                            >
                                @foreach ($partnerTypes as $type)
                                    <option value="{{ $type->value }}" {{ old('type', isset($partner) ? $partner->type->value : '') === $type->value ? 'selected' : '' }}>
                                        {{ $type->label() }}
                                    </option>
                                @endforeach
                            </x-forms.select>
                        </div>
                        <div class="col-md-6">
                            <x-forms.input
                                type="email"
                                name="email"
                                :label="__('partners.email')"
                                :value="old('email', $partner->email ?? '')"
                                placeholder="e.g. info@hotelbv.com"
                                :hint="__('partners.email_hint')"
                                required
                            />
                        </div>
                        <div class="col-md-6">
                            <x-forms.input
                                type="tel"
                                name="phone"
                                :label="__('partners.phone')"
                                :value="old('phone', $partner->phone ?? '')"
                                placeholder="e.g. +39 089 123456"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Company Info Card-->

            <!--begin::Billing Info Card-->
            <div class="card mb-5 mb-xl-10">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <span class="me-2">
                            <i class="ki-duotone ki-bill fs-2 text-primary">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                                <span class="path6"></span>
                            </i>
                        </span>
                        {{ __('partners.billing_information') }}
                    </div>
                </div>
                <div class="card-body py-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <x-forms.input
                                name="vat_number"
                                :label="__('partners.vat_number')"
                                :value="old('vat_number', $partner->vat_number ?? '')"
                                placeholder="e.g. IT01234567890"
                            />
                        </div>
                        <div class="col-md-6">
                            <x-forms.input
                                name="sdi_pec"
                                :label="__('partners.sdi_pec')"
                                :value="old('sdi_pec', $partner->sdi_pec ?? '')"
                                placeholder="e.g. ABCDEFG or pec@company.it"
                            />
                        </div>
                        <div class="col-12">
                            <x-forms.textarea
                                name="address"
                                :label="__('partners.address')"
                                :value="old('address', $partner->address ?? '')"
                                placeholder="Full billing address..."
                                rows="2"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Billing Info Card-->

            @if (isset($partner))
            <!--begin::Price List Card-->
            <div class="card mb-5 mb-xl-10" id="prices">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <span class="me-2">
                            <i class="ki-duotone ki-dollar fs-2 text-primary">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                        </span>
                        {{ __('partners.dedicated_price_list') }}
                    </div>
                    <div class="card-toolbar">
                        <span class="badge badge-light-warning">{{ __('partners.prices_not_visible') }}</span>
                    </div>
                </div>
                <div class="card-body py-4">
                    <!--begin::Notice-->
                    <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-6">
                        <i class="ki-duotone ki-information fs-2tx text-primary me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <div class="fs-6 text-gray-700">{{ __('partners.price_list_notice') }}</div>
                            </div>
                        </div>
                    </div>
                    <!--end::Notice-->

                    <!--begin::Tour Price Lists-->
                    <div class="accordion accordion-icon-toggle" id="accordionPrices">
                        @foreach ($tours as $tour)
                        <div class="mb-5">
                            <div class="accordion-header py-3 d-flex" data-bs-toggle="collapse" data-bs-target="#tour_price_{{ $tour->id }}">
                                <span class="accordion-icon">
                                    <i class="ki-duotone ki-arrow-right fs-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                                <h3 class="fs-5 fw-bold mb-0 ms-4">{{ $tour->name }} ({{ $tour->code }})</h3>
                            </div>
                            <div id="tour_price_{{ $tour->id }}" class="collapse fs-6 ps-10" data-bs-parent="#accordionPrices">
                                <div class="row g-4 mt-2">
                                    @foreach ($seasons as $season)
                                    <div class="col-12">
                                        <h6 class="text-muted mb-3{{ !$loop->first ? ' mt-3' : '' }}">{{ $season->label() }}</h6>
                                    </div>
                                    @foreach ($paxTypes as $paxType)
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold fs-7">{{ $paxType->label() }} ({{ $paxType->value }})</label>
                                        <div class="input-group input-group-solid input-group-sm">
                                            <span class="input-group-text">EUR</span>
                                            <input type="number"
                                                class="form-control form-control-solid"
                                                name="prices[{{ $tour->id }}][{{ $season->value }}][{{ $paxType->value }}]"
                                                value="{{ $priceMatrix[$tour->id][$season->value][$paxType->value] ?? '' }}"
                                                placeholder=""
                                                step="0.01"
                                                min="0"
                                                >
                                        </div>
                                    </div>
                                    @endforeach
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <!--end::Tour Price Lists-->
                </div>
            </div>
            <!--end::Price List Card-->
            @endif
        </div>
        <!--end::Main Column-->

        <!--begin::Sidebar-->
        <div class="col-xl-4">
            <!--begin::Status Card-->
            <div class="card mb-5 mb-xl-10">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">{{ __('partners.status') }}</div>
                </div>
                <div class="card-body py-4">
                    <x-forms.toggle
                        name="is_active"
                        :label="__('partners.partner_active')"
                        :checked="old('is_active', $partner->is_active ?? true)"
                    />
                    <div class="form-text mt-3">{{ __('partners.suspended_partner_hint') }}</div>
                </div>
            </div>
            <!--end::Status Card-->

            @if (!isset($partner))
            <!--begin::User Account Card-->
            <div class="card mb-5 mb-xl-10">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">{{ __('partners.user_account') }}</div>
                </div>
                <div class="card-body py-4">
                    <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4 mb-4">
                        <i class="ki-duotone ki-information fs-2tx text-info me-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <div class="fs-7 text-gray-700">{{ __('partners.user_account_notice') }}</div>
                            </div>
                        </div>
                    </div>
                    <x-forms.input
                        name="contact_name"
                        :label="__('partners.contact_name')"
                        :value="old('contact_name')"
                        placeholder="e.g. Mario Rossi"
                    />
                </div>
            </div>
            <!--end::User Account Card-->
            @endif

            <!--begin::Notes Card-->
            <div class="card mb-5 mb-xl-10">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">{{ __('partners.internal_notes') }}</div>
                </div>
                <div class="card-body py-4">
                    <x-forms.textarea
                        name="notes"
                        :value="old('notes', $partner->notes ?? '')"
                        placeholder="Internal notes about this partner..."
                        rows="4"
                        :hint="__('partners.notes_hint')"
                    />
                </div>
            </div>
            <!--end::Notes Card-->

            <!--begin::Actions Card-->
            <div class="card">
                <div class="card-body py-4">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ki-duotone ki-check fs-3 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            {{ isset($partner) ? __('partners.update_partner') : __('partners.save_partner') }}
                        </button>
                        <a href="{{ route('admin.partners.index') }}" class="btn btn-light">
                            <i class="ki-duotone ki-cross fs-3 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            {{ __('partners.cancel') }}
                        </a>
                    </div>
                </div>
            </div>
            <!--end::Actions Card-->
        </div>
        <!--end::Sidebar-->
    </div>
</form>

@endsection

@push('styles')
<style>
    .accordion-icon-toggle .accordion-icon {
        transition: transform 0.3s ease;
    }
    .accordion-icon-toggle[aria-expanded="true"] .accordion-icon,
    .accordion-icon-toggle:not(.collapsed) .accordion-icon {
        transform: rotate(90deg);
    }
</style>
@endpush

