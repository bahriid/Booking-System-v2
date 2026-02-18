@extends('emails.layouts.base')

@section('title', __('emails.partner_welcome_title'))

@section('content')
<div class="email-header">
    <h1>{{ __('emails.partner_welcome_header') }}</h1>
    <div class="subtitle">{{ __('emails.partner_welcome_subtitle') }}</div>
</div>

<div class="email-body">
    <div class="greeting">{{ __('emails.hello', ['name' => $contactName]) }}</div>

    <div class="message">
        {{ __('emails.partner_welcome_message', ['company' => $partner->name]) }}
    </div>

    <div class="info-box">
        <div class="info-box-header">{{ __('emails.your_login_credentials') }}</div>
        <div class="info-row">
            <span class="info-label">{{ __('emails.email') }}</span>
            <span class="info-value">{{ $partner->email }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">{{ __('emails.temporary_password') }}</span>
            <span class="info-value" style="font-family: monospace; font-size: 16px;">{{ $password }}</span>
        </div>
    </div>

    <div class="alert alert-warning">
        <strong>{{ __('emails.important') }}:</strong> {{ __('emails.change_password_notice') }}
    </div>

    <div class="info-box">
        <div class="info-box-header">{{ __('emails.what_you_can_do') }}</div>
        <div style="padding: 10px 0;">
            <p style="margin: 0 0 10px 0;"><strong>1.</strong> {{ __('emails.partner_feature_1') }}</p>
            <p style="margin: 0 0 10px 0;"><strong>2.</strong> {{ __('emails.partner_feature_2') }}</p>
            <p style="margin: 0 0 10px 0;"><strong>3.</strong> {{ __('emails.partner_feature_3') }}</p>
            <p style="margin: 0;"><strong>4.</strong> {{ __('emails.partner_feature_4') }}</p>
        </div>
    </div>

    <div class="divider"></div>

    <p style="text-align: center; margin-bottom: 25px;">
        <a href="{{ $loginUrl }}" class="btn btn-primary">{{ __('emails.login_now') }}</a>
    </p>

    <p style="text-align: center; font-size: 13px; color: #7e8299;">
        {{ __('emails.login_url') }}: <a href="{{ $loginUrl }}">{{ $loginUrl }}</a>
    </p>
</div>
@endsection
