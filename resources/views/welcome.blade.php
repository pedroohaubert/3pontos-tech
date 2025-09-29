@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h1 class="mt-8 text-2xl font-medium text-gray-900">
                        Hello World! 🎉
                    </h1>

                    <p class="mt-6 text-gray-500 leading-relaxed">
                        Welcome to our Reddit Clone! This is a temporary page to test the navigation and authentication system.
                    </p>

                    <div class="mt-8">
                        @guest
                            <p class="text-gray-600">Please log in or register to access the admin panel.</p>
                        @else
                            <p class="text-gray-600">
                                Welcome back, {{ auth()->user()->name }}!
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ url('/admin') }}" class="ml-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Go to Admin Panel
                                    </a>
                                @endif
                            </p>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection