@extends('layouts.main')

@section('content')
<div class="container mx-auto px-4 py-16">
    <div class="max-w-3xl mx-auto bg-gray-800 rounded-lg shadow-lg p-8 border border-gray-700 text-center">
        <div class="text-6xl font-bold text-red-500 mb-6">404</div>
        <h1 class="text-3xl font-bold text-white mb-4">Page Not Found</h1>
        <p class="text-gray-400 mb-8">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
        
        <div class="space-y-4">
            <p class="text-gray-300">Possible issues:</p>
            <ul class="text-left text-gray-400 mx-auto max-w-md list-disc pl-5 space-y-2">
                <li>Missing route configuration</li>
                <li>Missing view file or controller method</li>
                <li>URL path typo in the request</li>
                <li>Missing directory or file for resources</li>
            </ul>
        </div>
        
        <div class="mt-8">
            <a href="{{ route('movie.index') }}" class="inline-block movie-btn py-3 px-6 rounded-lg">
                Return to Homepage
            </a>
        </div>
    </div>
</div>
@endsection
