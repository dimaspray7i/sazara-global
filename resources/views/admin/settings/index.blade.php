@extends('admin.layouts.admin')

@section('title', 'Admin Settings')
@section('page-title', 'Site & Account Settings')
@section('breadcrumb')
    <span class="adm-breadcrumb-sep">/</span>
    <span>Settings</span>
@endsection

@section('content')
<div style="max-width: 820px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">

    {{-- Card 1: Global Site Settings (WhatsApp & Contact Info) --}}
    <div class="adm-card">
        <div class="adm-card-header">
            <div>
                <div class="adm-card-title">Global Site Settings</div>
                <div class="adm-text-muted adm-text-sm" style="margin-top: 2px;">
                    Manage the centralized WhatsApp number and contact details used across all public buttons and links.
                </div>
            </div>
            <span class="adm-badge adm-badge-green">Live Synced</span>
        </div>

        <div class="adm-card-body">
            <form method="POST" action="{{ route('admin.settings.site') }}" data-single-submit>
                @csrf
                @method('PUT')

                <div class="adm-form-grid">
                    <div class="adm-form-grid-2">
                        {{-- WhatsApp Number --}}
                        <div class="adm-field">
                            <label class="adm-label" for="whatsapp_number">
                                WhatsApp Number <span class="req">*</span>
                            </label>
                            <input class="adm-input" type="text" id="whatsapp_number" name="whatsapp_number"
                                   value="{{ old('whatsapp_number', $siteSettings['whatsapp_number'] ?? '+62 812-6040-7208') }}"
                                   required placeholder="+62 812-6040-7208">
                            <span class="adm-hint">
                                Target link preview: <strong style="color:var(--adm-green)">https://wa.me/{{ $siteSettings['whatsapp_clean'] ?? '6281260407208' }}</strong>
                            </span>
                        </div>

                        {{-- Contact Person / PIC --}}
                        <div class="adm-field">
                            <label class="adm-label" for="contact_person">Contact Person (PIC)</label>
                            <input class="adm-input" type="text" id="contact_person" name="contact_person"
                                   value="{{ old('contact_person', $siteSettings['contact_person'] ?? 'Afriansyah Munar') }}"
                                   placeholder="Afriansyah Munar">
                            <span class="adm-hint">Displayed in live chat widget & contact details.</span>
                        </div>
                    </div>

                    {{-- Default WhatsApp Message --}}
                    <div class="adm-field">
                        <label class="adm-label" for="whatsapp_message">Default WhatsApp Message</label>
                        <textarea class="adm-textarea" id="whatsapp_message" name="whatsapp_message" rows="2"
                                  placeholder="Hello Sazara Global, I would like to make an inquiry...">{{ old('whatsapp_message', $siteSettings['whatsapp_message'] ?? 'Hello Sazara Global, I would like to make an inquiry...') }}</textarea>
                        <span class="adm-hint">Pre-filled message when visitors click "Chat on WhatsApp" or "Get Offer" buttons.</span>
                    </div>

                    <div class="adm-form-grid-2">
                        {{-- Company Email --}}
                        <div class="adm-field">
                            <label class="adm-label" for="site_email">Company Email</label>
                            <input class="adm-input" type="email" id="site_email" name="email"
                                   value="{{ old('email', $siteSettings['email'] ?? 'contact@sazaraglobal.com') }}"
                                   placeholder="contact@sazaraglobal.com">
                        </div>

                        {{-- Phone --}}
                        <div class="adm-field">
                            <label class="adm-label" for="site_phone">Company Phone (Display)</label>
                            <input class="adm-input" type="text" id="site_phone" name="phone"
                                   value="{{ old('phone', $siteSettings['phone'] ?? '+62 812-6040-7208') }}"
                                   placeholder="+62 812-6040-7208">
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="adm-field">
                        <label class="adm-label" for="site_address">Company Address</label>
                        <input class="adm-input" type="text" id="site_address" name="address"
                               value="{{ old('address', $siteSettings['address'] ?? 'Medan, North Sumatra, Indonesia') }}"
                               placeholder="Medan, North Sumatra, Indonesia">
                    </div>
                </div>

                <div class="adm-divider"></div>

                <div class="adm-flex adm-gap-12" style="justify-content: flex-end;">
                    <button type="submit" class="adm-btn adm-btn-green">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        Save Site Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Card 2: Admin Account & Security --}}
    <div class="adm-card">
        <div class="adm-card-header">
            <div>
                <div class="adm-card-title">Manage Admin Account</div>
                <div class="adm-text-muted adm-text-sm" style="margin-top: 2px;">
                    Update your administrator profile name, login email, and password.
                </div>
            </div>
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

</div>
@endsection
