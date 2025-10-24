@extends('layouts.profile')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-globe text-blue-500 mr-2"></i>{{ __('messages.language.title') }}
    </h2>

    <p class="text-gray-600 mb-8">
        {{ __('messages.language.description') }}
    </p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($languages as $code => $language)
            <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition duration-200 {{ $currentLocale === $code ? 'border-blue-500 ring-2 ring-blue-500 bg-blue-50' : '' }}">
                <div class="text-center">
                    <div class="text-4xl mb-4">{{ $language['flag'] }}</div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $language['name'] }}</h3>
                    
                    @if($currentLocale === $code)
                        <div class="mb-4">
                            <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                <i class="fas fa-check mr-1"></i>{{ __('messages.language.current') }}
                            </span>
                        </div>
                    @endif

                    <form action="{{ route('language.change') }}" method="POST" class="inline-block">
                        @csrf
                        <input type="hidden" name="locale" value="{{ $code }}">
                        <button type="submit" 
                                class="w-full {{ $currentLocale === $code ? 'bg-blue-500 hover:bg-blue-600' : 'bg-gray-500 hover:bg-gray-600' }} text-white py-2 px-4 rounded-lg transition duration-200">
                            @if($currentLocale === $code)
                                <i class="fas fa-check mr-2"></i>{{ __('messages.language.selected') }}
                            @else
                                <i class="fas fa-language mr-2"></i>{{ __('messages.language.select') }}
                            @endif
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Language Info -->
    <div class="mt-8 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-info-circle text-blue-500 mr-2"></i>{{ __('messages.language.info_title') }}
        </h3>
        <div class="space-y-2 text-sm text-gray-600">
            <p>{{ __('messages.language.info_1') }}</p>
            <p>{{ __('messages.language.info_2') }}</p>
            <p>{{ __('messages.language.info_3') }}</p>
        </div>
    </div>
</div>
@endsection
