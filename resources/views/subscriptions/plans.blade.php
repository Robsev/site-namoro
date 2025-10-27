@extends('layouts.profile')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-crown text-yellow-500 mr-2"></i>{{ __('messages.subscriptions.subscription_plans') }}
    </h2>

    @if($currentSubscription)
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span class="font-semibold">{{ __('messages.subscriptions.current') }}:</span>
                <span class="ml-2">{{ $currentSubscription->plan === 'premium_monthly' ? __('messages.subscriptions.premium_monthly') : __('messages.subscriptions.premium_yearly') }}</span>
                <span class="ml-4 text-sm">{{ __('messages.subscriptions.expires_on') }} {{ $currentSubscription->ends_at->format('d/m/Y') }}</span>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($plans as $planKey => $plan)
            <div class="border border-gray-200 rounded-lg p-6 {{ $plan['popular'] ?? false ? 'border-pink-500 ring-2 ring-pink-500' : '' }} {{ $currentSubscription && $currentSubscription->plan === $planKey ? 'bg-green-50' : '' }}">
                @if($plan['popular'] ?? false)
                    <div class="text-center mb-4">
                        <span class="bg-pink-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            {{ __('messages.subscriptions.most_popular') }}
                        </span>
                    </div>
                @endif

                @if($currentSubscription && $currentSubscription->plan === $planKey)
                    <div class="text-center mb-4">
                        <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            {{ __('messages.subscriptions.current_plan') }}
                        </span>
                    </div>
                @endif

                <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $plan['name'] }}</h3>
                
                <div class="mb-4">
                    <span class="text-3xl font-bold text-gray-800">R$ {{ number_format($plan['price'], 2, ',', '.') }}</span>
                    @if($plan['interval'] !== 'forever')
                        <span class="text-gray-600">{{ __('messages.subscriptions.per_' . $plan['interval']) }}</span>
                    @endif
                </div>

                @if(isset($plan['savings']))
                    <div class="bg-yellow-100 text-yellow-800 px-3 py-2 rounded-lg text-sm font-semibold mb-4">
                        <i class="fas fa-gift mr-1"></i>{{ $plan['savings'] ?? '' }}
                    </div>
                @endif

                <ul class="space-y-3 mb-6">
                    @foreach($plan['features'] as $feature)
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span class="text-gray-700">{{ $feature }}</span>
                        </li>
                    @endforeach
                </ul>

                @if(isset($plan['limitations']))
                    <div class="mb-4">
                        <h4 class="text-sm font-semibold text-gray-600 mb-2">{{ __('messages.subscriptions.limitations') }}</h4>
                        <ul class="space-y-1">
                            @foreach($plan['limitations'] as $limitation)
                                <li class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-times text-red-400 mr-2"></i>
                                    {{ $limitation }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="text-center">
                    @if($currentSubscription && $currentSubscription->plan === $planKey)
                        <a href="{{ route('subscriptions.show') }}" 
                           class="w-full bg-green-500 text-white py-3 px-4 rounded-lg hover:bg-green-600 transition duration-200">
                            <i class="fas fa-eye mr-2"></i>{{ __('messages.subscriptions.manage') }}
                        </a>
                    @elseif($planKey === 'free')
                        <span class="w-full bg-gray-300 text-gray-600 py-3 px-4 rounded-lg cursor-not-allowed">
                            <i class="fas fa-check mr-2"></i>{{ __('messages.subscriptions.current_plan') }}
                        </span>
                    @else
                        <form action="{{ route('subscriptions.create') }}" method="POST" class="inline-block w-full">
                            @csrf
                            <input type="hidden" name="plan" value="{{ $planKey }}">
                            <input type="hidden" name="payment_method" value="credit_card">
                            <button type="submit" 
                                    class="w-full bg-pink-500 text-white py-3 px-4 rounded-lg hover:bg-pink-600 transition duration-200">
                                <i class="fas fa-credit-card mr-2"></i>{{ __('messages.subscriptions.subscribe_now') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- FAQ Section -->
    <div class="mt-12">
        <h3 class="text-xl font-bold text-gray-800 mb-6">{{ __('messages.subscriptions.faq') }}</h3>
        <div class="space-y-4">
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-800 mb-2">{{ __('messages.subscriptions.faq_cancel') }}</h4>
                <p class="text-gray-600">{{ __('messages.subscriptions.faq_cancel_answer') }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-800 mb-2">{{ __('messages.subscriptions.faq_refund') }}</h4>
                <p class="text-gray-600">{{ __('messages.subscriptions.faq_refund_answer') }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-800 mb-2">{{ __('messages.subscriptions.faq_payment') }}</h4>
                <p class="text-gray-600">{{ __('messages.subscriptions.faq_payment_answer') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
