@extends('layouts.auth')

@section('title', 'Giriş Yap')

@section('content')
@if(session('error'))
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-2"></i>
        {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
    </div>
@endif

<form method="POST" action="{{ route('login.submit') }}">
    @csrf

    <div class="input-group">
        <i class="fas fa-envelope"></i>
        <input type="email" 
               name="email" 
               id="email" 
               class="form-control @error('email') is-invalid @enderror" 
               placeholder="E-posta adresinizi girin"
               value="{{ old('email') }}"
               required 
               autofocus>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="input-group">
        <i class="fas fa-lock"></i>
        <input type="password" 
               name="password" 
               id="password" 
               class="form-control @error('password') is-invalid @enderror"
               placeholder="Şifrenizi girin"
               required>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-login">
        <i class="fas fa-sign-in-alt me-2"></i>
        Giriş Yap
    </button>
</form>

<div class="text-center mt-3">
    <small class="text-muted">
        <i class="fas fa-shield-alt me-1"></i>
        Güvenli giriş
    </small>
</div>
@endsection
