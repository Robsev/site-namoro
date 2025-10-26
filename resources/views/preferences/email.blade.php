@extends('layouts.profile')

@section('title', __('messages.email.title'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">
            <i class="fas fa-envelope mr-2"></i>{{ __('messages.email.title') }}
        </h1>
        <p class="text-gray-600 mb-8">{{ __('messages.email.description') }}</p>

        <form method="POST" action="{{ route('email-preferences.update') }}" class="space-y-8">
            @csrf

            <!-- Global Email Toggle -->
            <div class="space-y-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('messages.email.global_settings') }}</h2>
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">{{ __('messages.email.enable_notifications') }}</h3>
                            <p class="text-sm text-gray-500">{{ __('messages.email.enable_notifications_desc') }}</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="email_notifications_enabled" value="1" 
                                   {{ old('email_notifications_enabled', $user->email_notifications_enabled ?? true) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Specific Notifications -->
            <div class="space-y-4">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('messages.email.notification_types') }}</h2>
                <p class="text-sm text-gray-600">{{ __('messages.email.notification_types_desc') }}</p>
                
                <div class="space-y-6">
                    <!-- New Matches -->
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-heart text-pink-500 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-900">{{ __('messages.email.new_matches') }}</h3>
                                <p class="text-sm text-gray-500">{{ __('messages.email.new_matches_desc') }}</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="email_new_matches" value="1" 
                                   {{ old('email_new_matches', $user->email_new_matches ?? true) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                        </label>
                    </div>

                    <!-- New Likes -->
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-thumbs-up text-blue-500 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-900">{{ __('messages.email.new_likes') }}</h3>
                                <p class="text-sm text-gray-500">{{ __('messages.email.new_likes_desc') }}</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="email_new_likes" value="1" 
                                   {{ old('email_new_likes', $user->email_new_likes ?? true) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                        </label>
                    </div>

                    <!-- New Messages -->
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-comment text-green-500 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-900">{{ __('messages.email.new_messages') }}</h3>
                                <p class="text-sm text-gray-500">{{ __('messages.email.new_messages_desc') }}</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="email_new_messages" value="1" 
                                   {{ old('email_new_messages', $user->email_new_messages ?? true) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                        </label>
                    </div>

                    <!-- Photo Approvals -->
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-camera text-purple-500 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-900">{{ __('messages.email.photo_approvals') }}</h3>
                                <p class="text-sm text-gray-500">{{ __('messages.email.photo_approvals_desc') }}</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="email_photo_approvals" value="1" 
                                   {{ old('email_photo_approvals', $user->email_photo_approvals ?? true) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                        </label>
                    </div>

                    <!-- Marketing -->
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-bullhorn text-orange-500 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-900">{{ __('messages.email.marketing') }}</h3>
                                <p class="text-sm text-gray-500">{{ __('messages.email.marketing_desc') }}</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="email_marketing" value="1" 
                                   {{ old('email_marketing', $user->email_marketing ?? false) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Information Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">{{ __('messages.email.important') }}</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>{{ __('messages.email.important_system') }}</li>
                                <li>{{ __('messages.email.important_customize') }}</li>
                                <li>{{ __('messages.email.important_address') }} <strong>{{ $user->email }}</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-6 border-t border-gray-200">
                <button type="submit" class="bg-pink-600 text-white px-8 py-3 rounded-lg hover:bg-pink-700 transition duration-200 font-medium">
                    <i class="fas fa-save mr-2"></i>{{ __('messages.email.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const globalToggle = document.querySelector('input[name="email_notifications_enabled"]');
    const specificToggles = document.querySelectorAll('input[name^="email_"]:not([name="email_notifications_enabled"])');
    
    // Function to update specific toggles based on global toggle
    function updateSpecificToggles() {
        const isEnabled = globalToggle.checked;
        specificToggles.forEach(toggle => {
            toggle.disabled = !isEnabled;
            if (!isEnabled) {
                toggle.checked = false;
            }
        });
    }
    
    // Initial state
    updateSpecificToggles();
    
    // Listen for global toggle changes
    globalToggle.addEventListener('change', updateSpecificToggles);
});
</script>
@endsection
