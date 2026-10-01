@extends('admin.layouts.admin')

@section('title', 'Admin Settings')
@section('page-title', 'Account Settings')
@section('breadcrumb')
    <span class="adm-breadcrumb-sep">/</span>
    <span>Settings</span>
@endsection

@section('content')
<div class="adm-card" style="max-width: 680px; margin: 0 auto;">
    <div class="adm-card-header">
        <div class="adm-card-title">Manage Admin Account</div>
    </div>

    <div class="adm-card-body">
        <form method="POST" action="{{ route('admin.settings.update') }}" data-single-submit>
            @csrf
            @method('PUT')

            <div class="adm-form-grid">
                <div class="adm-field">
                    <label class="adm-label" for="name">Admin Name <span class="req">*</span></label>
                    <input class="adm-input" type="text" id="name" name="name" value="{{ old('name', $user->name ?? '') }}" required>
                </div>

                <div class="adm-field">
                    <label class="adm-label" for="email">Email Address <span class="req">*</span></label>
                    <input class="adm-input" type="email" id="email" name="email" value="{{ old('email', $user->email ?? '') }}" required>
                </div>

                <div class="adm-divider"></div>
                <div class="adm-text-muted adm-text-sm" style="margin-top: -10px; margin-bottom: 10px;">
                    <strong>Change Password:</strong> Leave blank if you don't wish to change your current password.
                </div>

                <div class="adm-field">
                    <label class="adm-label" for="current_password">Current Password</label>
                    <input class="adm-input" type="password" id="current_password" name="current_password" placeholder="Enter current password if changing">
                </div>

                <div class="adm-form-grid-2">
                    <div class="adm-field">
                        <label class="adm-label" for="new_password">New Password</label>
                        <input class="adm-input" type="password" id="new_password" name="new_password" placeholder="Min. 8 characters">
                    </div>
                    <div class="adm-field">
                        <label class="adm-label" for="new_password_confirmation">Confirm New Password</label>
                        <input class="adm-input" type="password" id="new_password_confirmation" name="new_password_confirmation" placeholder="Re-type new password">
                    </div>
                </div>
            </div>

            <div class="adm-divider"></div>

            <div class="adm-flex adm-gap-12" style="justify-content: flex-end;">
                <button type="submit" class="adm-btn adm-btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Update Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
