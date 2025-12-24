@extends('layouts.public')

@section('title', 'Too Many Requests')

@section('content')
<div class="max-w-2xl mx-auto text-center py-16 px-4">
    <div class="mb-8">
        <svg class="w-24 h-24 mx-auto text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
</div>

<h1 class="text-4xl font-bold text-gray-900 mb-4">
    Slow Down! 🐌
</h1>

<p class="text-xl text-gray-600 mb-8">
            You're submitting comments too quickly!
</p>

<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8">
    <p class="text-gray-700 mb-2">
                    To prevent spam, we limit comments to <strong>5 per minute</strong>.
</p>
<p class="text-gray-600 text-sm">
                Please wait a moment before commenting again.
</p>
</div>

<a href="{{ url()->previous() }}" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition">
            ← Go Back
</a>
</div>
@endsection

