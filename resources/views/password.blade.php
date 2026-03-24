@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4 text-center">Password Required</h2>
        <div class="text-center text-red-600 font-semibold mb-2">Password required</div>
        <div class="mb-2 text-center text-gray-600">
            <span class="font-semibold">Short link:</span> <span class="text-blue-600">{{ $short_code ?? '' }}</span><br>
            <span class="font-semibold">Destination:</span> <span class="truncate">{{ $original_url ? Str::limit($original_url, 40) : '' }}</span>
        </div>
        @if($error)
            <div class="mb-4 text-red-600">{{ $error }}</div>
        @endif
        <form method="POST" action="" id="password-form">
            @csrf
            <div class="mb-4">
                <label for="password" class="block text-gray-700">Enter password to access this link:</label>
                <div class="relative">
                    <input type="password" name="password" id="password" class="w-full border rounded px-3 py-2 mt-1 pr-10" required autofocus autocomplete="current-password">
                    <button type="button" id="toggle-password" class="absolute right-2 top-2 text-gray-500">Show</button>
                </div>
            </div>
            <button id="submit-btn" type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Submit</button>
        </form>
        <a href="/" class="block text-center text-blue-500 mt-4">&larr; Back to Home</a>
    </div>
</div>
@push('scripts')
<script>
    document.getElementById('password').focus();
    document.getElementById('toggle-password').onclick = function() {
        const input = document.getElementById('password');
        if (input.type === 'password') {
            input.type = 'text';
            this.textContent = 'Hide';
        } else {
            input.type = 'password';
            this.textContent = 'Show';
        }
    };
    document.getElementById('password-form').onsubmit = function() {
        document.getElementById('submit-btn').disabled = true;
        document.getElementById('submit-btn').textContent = 'Loading...';
    };
</script>
@endpush
@endsection
