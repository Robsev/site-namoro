@extends('layouts.profile')

@section('title', 'Perfil Psicológico')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('messages.profile.psychological_title') }}</h1>
        <p class="mt-2 text-gray-600">{{ __('messages.profile.psychological_description') }}</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($profile && $profile->completed_at)
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                <div>
                    <p class="text-blue-800 font-medium">{{ __('messages.profile.psychological_completed') }}</p>
                    <p class="text-blue-700 text-sm">{{ __('messages.profile.psychological_last_update') }}: {{ $profile->completed_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('psychological-profile.store') }}" class="space-y-8">
        @csrf
        
        <!-- Big Five - Openness -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-lightbulb text-purple-500 mr-2"></i>
                {{ __('messages.psychological.openness') }}
            </h2>
            <p class="text-gray-600 text-sm mb-6">{{ __('messages.psychological.openness_desc') }}</p>
            
            <div class="space-y-4">
                <div class="question">
                    <p class="text-gray-700 mb-3">{{ __('messages.psychological.question1') }}</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[1]" value="1" class="mr-1.5"> {{ __('messages.psychological.strongly_disagree') }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[1]" value="2" class="mr-1.5"> {{ __('messages.psychological.disagree') }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[1]" value="3" class="mr-1.5"> {{ __('messages.psychological.neutral') }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[1]" value="4" class="mr-1.5"> {{ __('messages.psychological.agree') }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[1]" value="5" class="mr-1.5"> {{ __('messages.psychological.strongly_agree') }}</label>
                    </div>
                </div>
                
                <div class="question">
                    <p class="text-gray-700 mb-3">{{ __('messages.psychological.question2') }}</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[2]" value="1" class="mr-1.5">{{ __("messages.psychological.strongly_disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[2]" value="2" class="mr-1.5">{{ __("messages.psychological.disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[2]" value="3" class="mr-1.5">{{ __("messages.psychological.neutral") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[2]" value="4" class="mr-1.5">{{ __("messages.psychological.agree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[2]" value="5" class="mr-1.5">{{ __("messages.psychological.agree") }} totalmente</label>
                    </div>
                </div>
                
                <div class="question">
                    <p class="text-gray-700 mb-3">{{ __('messages.psychological.question3') }}</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[3]" value="1" class="mr-1.5">{{ __("messages.psychological.strongly_disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[3]" value="2" class="mr-1.5">{{ __("messages.psychological.disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[3]" value="3" class="mr-1.5">{{ __("messages.psychological.neutral") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[3]" value="4" class="mr-1.5">{{ __("messages.psychological.agree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[3]" value="5" class="mr-1.5">{{ __("messages.psychological.agree") }} totalmente</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Big Five - Conscientiousness -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-tasks text-green-500 mr-2"></i>
               {{ __("messages.psychological.conscientiousness") }}
            </h2>
            <p class="text-gray-600 text-sm mb-6">{{ __("messages.psychological.conscientiousness_desc") }}</p>
            
            <div class="space-y-4">
                <div class="question">
                    <p class="text-gray-700 mb-3">{{ __('messages.psychological.question4') }}</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[4]" value="1" class="mr-1.5">{{ __("messages.psychological.strongly_disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[4]" value="2" class="mr-1.5">{{ __("messages.psychological.disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[4]" value="3" class="mr-1.5">{{ __("messages.psychological.neutral") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[4]" value="4" class="mr-1.5">{{ __("messages.psychological.agree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[4]" value="5" class="mr-1.5">{{ __("messages.psychological.agree") }} totalmente</label>
                    </div>
                </div>
                
                <div class="question">
                    <p class="text-gray-700 mb-3">{{ __('messages.psychological.question5') }}</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[5]" value="1" class="mr-1.5">{{ __("messages.psychological.strongly_disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[5]" value="2" class="mr-1.5">{{ __("messages.psychological.disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[5]" value="3" class="mr-1.5">{{ __("messages.psychological.neutral") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[5]" value="4" class="mr-1.5">{{ __("messages.psychological.agree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[5]" value="5" class="mr-1.5">{{ __("messages.psychological.agree") }} totalmente</label>
                    </div>
                </div>
                
                <div class="question">
                    <p class="text-gray-700 mb-3">{{ __("messages.psychological.question6") }}</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[6]" value="1" class="mr-1.5">{{ __("messages.psychological.strongly_disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[6]" value="2" class="mr-1.5">{{ __("messages.psychological.disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[6]" value="3" class="mr-1.5">{{ __("messages.psychological.neutral") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[6]" value="4" class="mr-1.5">{{ __("messages.psychological.agree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[6]" value="5" class="mr-1.5">{{ __("messages.psychological.agree") }} totalmente</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Big Five - Extraversion -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-users text-yellow-500 mr-2"></i>
               {{ __("messages.psychological.extraversion") }}
            </h2>
            <p class="text-gray-600 text-sm mb-6">{{ __("messages.psychological.extraversion_desc") }}</p>
            
            <div class="space-y-4">
                <div class="question">
                    <p class="text-gray-700 mb-3">{{ __("messages.psychological.question7") }}</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[7]" value="1" class="mr-1.5">{{ __("messages.psychological.strongly_disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[7]" value="2" class="mr-1.5">{{ __("messages.psychological.disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[7]" value="3" class="mr-1.5">{{ __("messages.psychological.neutral") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[7]" value="4" class="mr-1.5">{{ __("messages.psychological.agree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[7]" value="5" class="mr-1.5">{{ __("messages.psychological.agree") }} totalmente</label>
                    </div>
                </div>
                
                <div class="question">
                    <p class="text-gray-700 mb-3">{{ __("messages.psychological.question8") }}</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[8]" value="1" class="mr-1.5">{{ __("messages.psychological.strongly_disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[8]" value="2" class="mr-1.5">{{ __("messages.psychological.disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[8]" value="3" class="mr-1.5">{{ __("messages.psychological.neutral") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[8]" value="4" class="mr-1.5">{{ __("messages.psychological.agree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[8]" value="5" class="mr-1.5">{{ __("messages.psychological.agree") }} totalmente</label>
                    </div>
                </div>
                
                <div class="question">
                    <p class="text-gray-700 mb-3">{{ __("messages.psychological.question9") }}</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[9]" value="1" class="mr-1.5">{{ __("messages.psychological.strongly_disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[9]" value="2" class="mr-1.5">{{ __("messages.psychological.disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[9]" value="3" class="mr-1.5">{{ __("messages.psychological.neutral") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[9]" value="4" class="mr-1.5">{{ __("messages.psychological.agree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[9]" value="5" class="mr-1.5">{{ __("messages.psychological.agree") }} totalmente</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Big Five - Agreeableness -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-heart text-red-500 mr-2"></i>
               {{ __("messages.psychological.agreeableness") }}
            </h2>
            <p class="text-gray-600 text-sm mb-6">{{ __("messages.psychological.agreeableness_desc") }}</p>
            
            <div class="space-y-4">
                <div class="question">
                    <p class="text-gray-700 mb-3">{{ __("messages.psychological.question10") }}</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[10]" value="1" class="mr-1.5">{{ __("messages.psychological.strongly_disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[10]" value="2" class="mr-1.5">{{ __("messages.psychological.disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[10]" value="3" class="mr-1.5">{{ __("messages.psychological.neutral") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[10]" value="4" class="mr-1.5">{{ __("messages.psychological.agree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[10]" value="5" class="mr-1.5">{{ __("messages.psychological.agree") }} totalmente</label>
                    </div>
                </div>
                
                <div class="question">
                    <p class="text-gray-700 mb-3">{{ __("messages.psychological.question11") }}</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[11]" value="1" class="mr-1.5">{{ __("messages.psychological.strongly_disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[11]" value="2" class="mr-1.5">{{ __("messages.psychological.disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[11]" value="3" class="mr-1.5">{{ __("messages.psychological.neutral") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[11]" value="4" class="mr-1.5">{{ __("messages.psychological.agree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[11]" value="5" class="mr-1.5">{{ __("messages.psychological.agree") }} totalmente</label>
                    </div>
                </div>
                
                <div class="question">
                    <p class="text-gray-700 mb-3">{{ __("messages.psychological.question12") }}</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[12]" value="1" class="mr-1.5">{{ __("messages.psychological.strongly_disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[12]" value="2" class="mr-1.5">{{ __("messages.psychological.disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[12]" value="3" class="mr-1.5">{{ __("messages.psychological.neutral") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[12]" value="4" class="mr-1.5">{{ __("messages.psychological.agree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[12]" value="5" class="mr-1.5">{{ __("messages.psychological.agree") }} totalmente</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Big Five - Neuroticism -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-shield-alt text-blue-500 mr-2"></i>
                {{ __('messages.psychological.neuroticism') }}
            </h2>
            <p class="text-gray-600 text-sm mb-6">{{ __("messages.psychological.neuroticism_desc") }}</p>
            
            <div class="space-y-4">
                <div class="question">
                    <p class="text-gray-700 mb-3">{{ __("messages.psychological.question13") }}</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[13]" value="1" class="mr-1.5">{{ __("messages.psychological.strongly_disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[13]" value="2" class="mr-1.5">{{ __("messages.psychological.disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[13]" value="3" class="mr-1.5">{{ __("messages.psychological.neutral") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[13]" value="4" class="mr-1.5">{{ __("messages.psychological.agree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[13]" value="5" class="mr-1.5">{{ __("messages.psychological.agree") }} totalmente</label>
                    </div>
                </div>
                
                <div class="question">
                    <p class="text-gray-700 mb-3">{{ __("messages.psychological.question14") }}</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[14]" value="1" class="mr-1.5">{{ __("messages.psychological.strongly_disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[14]" value="2" class="mr-1.5">{{ __("messages.psychological.disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[14]" value="3" class="mr-1.5">{{ __("messages.psychological.neutral") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[14]" value="4" class="mr-1.5">{{ __("messages.psychological.agree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[14]" value="5" class="mr-1.5">{{ __("messages.psychological.agree") }} totalmente</label>
                    </div>
                </div>
                
                <div class="question">
                    <p class="text-gray-700 mb-3">{{ __("messages.psychological.question15") }}</p>
                    <div class="flex flex-wrap gap-2 sm:gap-4">
                        <label class="flex items-center text-sm"><input type="radio" name="responses[15]" value="1" class="mr-1.5">{{ __("messages.psychological.strongly_disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[15]" value="2" class="mr-1.5">{{ __("messages.psychological.disagree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[15]" value="3" class="mr-1.5">{{ __("messages.psychological.neutral") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[15]" value="4" class="mr-1.5">{{ __("messages.psychological.agree") }}</label>
                        <label class="flex items-center text-sm"><input type="radio" name="responses[15]" value="5" class="mr-1.5">{{ __("messages.psychological.agree") }} totalmente</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configurações de Privacidade -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                <i class="fas fa-lock text-gray-500 mr-2"></i>
                {{ __('messages.psychological.privacy.title') }}
            </h2>
            
            <div class="flex items-center">
                <input type="checkbox" id="is_public" name="is_public" value="1" class="mr-3">
                <label for="is_public" class="text-gray-700">
                    {{ __('messages.psychological.privacy.description') }}
                </label>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-pink-600 text-white px-8 py-3 rounded-lg hover:bg-pink-700 transition duration-200 font-medium">
                <i class="fas fa-brain mr-2"></i>{{ __('messages.profile.save_psychological_profile') }}
            </button>
        </div>
    </form>
</div>

<style>
.question {
    border-bottom: 1px solid #f3f4f6;
    padding-bottom: 1rem;
}

.question:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

input[type="radio"] {
    accent-color: #ec4899;
}
</style>
@endsection
