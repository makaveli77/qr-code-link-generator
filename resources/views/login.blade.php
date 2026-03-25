@extends('layouts.app')

@section('content')
<div style="background-color: #0B0F1A; min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: sans-serif; color: white;">
    <div style="background-color: #111827; padding: 40px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); width: 100%; max-width: 400px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
        
        <div style="text-align: center; margin-bottom: 30px;">
             <h2 style="font-size: 24px; font-weight: 800; text-transform: uppercase; letter-spacing: -1px; margin-bottom: 5px;">Welcome Back</h2>
             <p style="color: #6B7280; font-size: 10px; font-weight: 700; text-transform: uppercase;">QR Code Generator</p>
        </div>

        @if(session('error'))
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); padding: 10px; border-radius: 10px; color: #EF4444; font-size: 12px; margin-bottom: 20px; text-align: center;">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 10px; font-weight: 800; color: #9CA3AF; text-transform: uppercase; margin-bottom: 8px;">User Email</label>
                <input type="email" name="email" required autofocus value="{{ old('email') }}"
                    style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 12px; border-radius: 12px; color: white !important; outline: none; box-sizing: border-box;"
                    placeholder="user@example.com">
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display: block; font-size: 10px; font-weight: 800; color: #9CA3AF; text-transform: uppercase; margin-bottom: 8px;">Password</label>
                <input type="password" name="password" required 
                    style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 12px; border-radius: 12px; color: white !important; outline: none; box-sizing: border-box;"
                    placeholder="••••••••">
            </div>

            <button type="submit" style="width: 100%; background: #2563EB; color: white; padding: 14px; border: none; border-radius: 12px; font-weight: 800; text-transform: uppercase; cursor: pointer; transition: 0.2s;">
                Login
            </button>
        </form>

        <div style="margin-top: 25px; text-align: center;">
            <a href="/register" style="color: #3B82F6; font-size: 12px; text-decoration: none; font-weight: 600;">Create an Account</a>
        </div>
    </div>
</div>
@endsection
