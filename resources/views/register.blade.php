@extends('layouts.app')

@section('content')
<div style="background-color: #0B0F1A; min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: sans-serif; color: white;">
    <div style="background-color: #111827; padding: 40px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); width: 100%; max-width: 440px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
        
        <div style="text-align: center; margin-bottom: 30px;">
             <h2 style="font-size: 24px; font-weight: 800; text-transform: uppercase; letter-spacing: -1px; margin-bottom: 5px;">Register</h2>
             <p style="color: #6B7280; font-size: 10px; font-weight: 700; text-transform: uppercase;">QR Code Generator</p>
        </div>

        @if ($errors->any())
            <div style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                <ul style="color: #F87171; font-size: 12px; font-weight: 600; margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="/register">
            @csrf
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 10px; font-weight: 800; color: #9CA3AF; text-transform: uppercase; margin-bottom: 8px;">Full Name</label>
                <input type="text" name="name" required autofocus value="{{ old('name') }}"
                    style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 12px; border-radius: 12px; color: white !important; outline: none; box-sizing: border-box;"
                    placeholder="John Doe">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-size: 10px; font-weight: 800; color: #9CA3AF; text-transform: uppercase; margin-bottom: 8px;">Email Address</label>
                <input type="email" name="email" required value="{{ old('email') }}"
                    style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 12px; border-radius: 12px; color: white !important; outline: none; box-sizing: border-box;"
                    placeholder="user@example.com">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
                <div>
                    <label style="display: block; font-size: 10px; font-weight: 800; color: #9CA3AF; text-transform: uppercase; margin-bottom: 8px;">Password</label>
                    <input type="password" name="password" required 
                        style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 12px; border-radius: 12px; color: white !important; outline: none; box-sizing: border-box;"
                        placeholder="••••••••">
                </div>
                <div>
                    <label style="display: block; font-size: 10px; font-weight: 800; color: #9CA3AF; text-transform: uppercase; margin-bottom: 8px;">Verify</label>
                    <input type="password" name="password_confirmation" required 
                        style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 12px; border-radius: 12px; color: white !important; outline: none; box-sizing: border-box;"
                        placeholder="••••••••">
                </div>
            </div>

            <div style="margin-bottom: 25px; display: flex; align-items: center; gap: 10px; background: rgba(79, 70, 229, 0.1); padding: 12px; border-radius: 12px; border: 1px solid rgba(79, 70, 229, 0.2);">
                <input type="checkbox" name="is_partner" id="is_partner" value="1" {{ old('is_partner') ? 'checked' : '' }} style="width: 18px; height: 18px; cursor: pointer; accent-color: #4F46E5;">
                <label for="is_partner" style="font-size: 11px; font-weight: 700; color: #E5E7EB; cursor: pointer; text-transform: uppercase; letter-spacing: 0.5px;">Become a Partner (Enables API Access)</label>
            </div>

            <button type="submit" style="width: 100%; background: #4F46E5; color: white; padding: 14px; border: none; border-radius: 12px; font-weight: 800; text-transform: uppercase; cursor: pointer; transition: 0.2s;">
                Register
            </button>
        </form>

        <div style="margin-top: 25px; text-align: center;">
            <a href="/login" style="color: #6366F1; font-size: 12px; text-decoration: none; font-weight: 600;">Already have an account? Login</a>
        </div>
    </div>
</div>
@endsection
