<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Sazara Global</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    @vite(['resources/css/admin.css'])
</head>
<body>
<div class="adm-login-page">
    <div class="adm-login-box">

        <div class="adm-login-brand">
            <div class="adm-login-logo-wrap">
                <img class="adm-login-logo" src="{{ asset('images/logo.jpg') }}" alt="PT Sazara Global Trade Logo" width="1024" height="1024">
            </div>
            <h1 class="adm-login-title">Sazara Global</h1>
            <p class="adm-login-sub">Content Management System</p>
        </div> 

        @if(session('success'))
            <div class="adm-alert adm-alert-success" style="margin-bottom:16px">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="adm-alert adm-alert-error" style="margin-bottom:16px">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="adm-alert adm-alert-error" style="margin-bottom:16px">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
                {{ $errors->first('email') ?: $errors->first() }}
            </div>
        @endif

        <form class="adm-login-form" method="POST" action="{{ route('admin.login.post') }}">
            @csrf

            <div class="adm-field">
                <label class="adm-label" for="email">Email Address</label>
                <input class="adm-input" type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       required autocomplete="email" autofocus
                       placeholder="admin@sazaraglobal.com">
            </div>

            <div class="adm-field">
                <label class="adm-label" for="password">Password</label>
                <input class="adm-input" type="password" id="password" name="password"
                       required autocomplete="current-password"
                       placeholder="Enter password">
            </div>

            <button type="submit" class="adm-btn adm-btn-primary" style="width:100%;justify-content:center;padding:11px">
                Sign In to Admin Panel
            </button>
        </form>

        <p style="text-align:center;margin-top:20px;font-size:12px;color:#5D6670">
            This is a private admin area. Unauthorized access is prohibited.
        </p>
    </div>
</div>
</body>
</html>
