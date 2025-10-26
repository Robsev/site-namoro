<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.auth.register') }} - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-pink-100 to-purple-100 min-h-screen flex items-center justify-center py-8">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-2xl">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ __('messages.register.title') }}</h1>
            <p class="text-gray-600">{{ __('messages.auth.register_subtitle') }}</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf
            
            <!-- Personal Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.register.first_name') }} *</label>
                    <input type="text" 
                           id="first_name" 
                           name="first_name" 
                           value="{{ old('first_name') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                           required>
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.register.last_name') }} *</label>
                    <input type="text" 
                           id="last_name" 
                           name="last_name" 
                           value="{{ old('last_name') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                           required>
                </div>
            </div>

            <!-- Contact Information -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.auth.email') }} *</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                       required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.auth.password') }} *</label>
                    <input type="password" 
                           id="password" 
                           name="password"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                           required>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.auth.password_confirmation') }} *</label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                           required>
                </div>
            </div>

            <!-- Personal Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.register.birth_date') }} *</label>
                    
                    <!-- Alternative: Dropdowns for better UX -->
                    <div id="birth_date_dropdown" class="grid grid-cols-3 gap-2">
                        <div>
                            <label for="birth_day" class="sr-only">{{ __('messages.register.birth_day') }}</label>
                            <select id="birth_day" 
                                    name="birth_day"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent text-sm">
                                <option value="">{{ __('messages.register.day') }}</option>
                                @for($day = 1; $day <= 31; $day++)
                                    <option value="{{ str_pad($day, 2, '0', STR_PAD_LEFT) }}" {{ old('birth_day') == $day ? 'selected' : '' }}>{{ $day }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label for="birth_month" class="sr-only">{{ __('messages.register.birth_month') }}</label>
                            <select id="birth_month" 
                                    name="birth_month"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent text-sm">
                                <option value="">{{ __('messages.register.month') }}</option>
                                @for($month = 1; $month <= 12; $month++)
                                    <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}" {{ old('birth_month') == $month ? 'selected' : '' }}>{{ $month }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label for="birth_year" class="sr-only">{{ __('messages.register.birth_year') }}</label>
                            <select id="birth_year" 
                                    name="birth_year"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent text-sm">
                                <option value="">{{ __('messages.register.year') }}</option>
                                @for($year = date('Y') - 18; $year >= 1920; $year--)
                                    <option value="{{ $year }}" {{ old('birth_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    
                    <!-- Hidden input for the actual birth_date value -->
                    <input type="hidden" 
                           id="birth_date" 
                           name="birth_date" 
                           value="{{ old('birth_date') }}"
                           required>
                    
                    <p class="mt-1 text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        {{ __('messages.register.birth_date_hint') }}
                    </p>
                </div>

                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.register.gender') }} *</label>
                    <select id="gender" 
                            name="gender"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                            required>
                        <option value="">{{ __('messages.common.select') }}</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{ __('messages.register.gender_male') }}</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{ __('messages.register.gender_female') }}</option>
                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>{{ __('messages.register.gender_other') }}</option>
                        <option value="prefer_not_to_say" {{ old('gender') == 'prefer_not_to_say' ? 'selected' : '' }}>{{ __('messages.register.gender_prefer_not_to_say') }}</option>
                    </select>
                </div>
            </div>

            <!-- Optional Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.register.phone') }}</label>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.register.location') }}</label>
                    <input type="text" 
                           id="location" 
                           name="location" 
                           value="{{ old('location') }}"
                           placeholder="{{ __('messages.register.location_placeholder') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                </div>
            </div>

            <!-- Terms and Conditions -->
            <div class="flex items-start">
                <input type="checkbox" 
                       id="terms" 
                       name="terms"
                       class="mt-1 rounded border-gray-300 text-pink-600 focus:ring-pink-500"
                       required>
                <label for="terms" class="ml-2 text-sm text-gray-600">
                    {{ __('messages.register.terms_agreement') }}
                    <a href="#" class="text-pink-600 hover:text-pink-500">{{ __('messages.register.terms_of_use') }}</a> 
                    {{ __('messages.common.and') }}
                    <a href="{{ route('privacy-policy') }}" class="text-pink-600 hover:text-pink-500">{{ __('messages.register.privacy_policy') }}</a>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full bg-gradient-to-r from-pink-500 to-purple-600 text-white py-3 rounded-lg font-medium hover:from-pink-600 hover:to-purple-700 transition duration-200">
                {{ __('messages.auth.register') }}
            </button>

            <!-- Login Link -->
            <div class="text-center">
                <p class="text-gray-600">
                    {{ __('messages.auth.already_have_account') }} 
                    <a href="{{ route('login') }}" class="text-pink-600 hover:text-pink-500 font-medium">{{ __('messages.auth.login_here') }}</a>
                </p>
            </div>
        </form>

        <!-- Language Selector -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="flex justify-center space-x-4">
                <form action="{{ route('language.change') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="locale" value="pt_BR">
                    <button type="submit" class="text-sm {{ app()->getLocale() === 'pt_BR' ? 'text-pink-600 font-medium' : 'text-gray-500 hover:text-gray-700' }}">
                        🇧🇷 Português
                    </button>
                </form>
                <form action="{{ route('language.change') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="locale" value="en">
                    <button type="submit" class="text-sm {{ app()->getLocale() === 'en' ? 'text-pink-600 font-medium' : 'text-gray-500 hover:text-gray-700' }}">
                        🇺🇸 English
                    </button>
                </form>
                <form action="{{ route('language.change') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="locale" value="es">
                    <button type="submit" class="text-sm {{ app()->getLocale() === 'es' ? 'text-pink-600 font-medium' : 'text-gray-500 hover:text-gray-700' }}">
                        🇪🇸 Español
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript for birth date dropdowns -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const birthDay = document.getElementById('birth_day');
            const birthMonth = document.getElementById('birth_month');
            const birthYear = document.getElementById('birth_year');
            const birthDate = document.getElementById('birth_date');

            function updateBirthDate() {
                const day = birthDay.value;
                const month = birthMonth.value;
                const year = birthYear.value;

                if (day && month && year) {
                    // Validate the date
                    const date = new Date(year, month - 1, day);
                    if (date.getDate() == day && date.getMonth() == month - 1 && date.getFullYear() == year) {
                        birthDate.value = `${year}-${month}-${day}`;
                        birthDate.setCustomValidity('');
                    } else {
                        birthDate.setCustomValidity('{{ __('messages.validation.date_invalid') }}');
                    }
                } else {
                    birthDate.value = '';
                    birthDate.setCustomValidity('');
                }
            }

            birthDay.addEventListener('change', updateBirthDate);
            birthMonth.addEventListener('change', updateBirthDate);
            birthYear.addEventListener('change', updateBirthDate);
        });
    </script>
</body>
</html>


