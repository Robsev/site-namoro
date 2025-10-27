@extends('layouts.profile')

@section('title', __('messages.profile.edit'))

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Profile Completeness Indicator -->
    @include('profile.completeness-indicator')
    
    <!-- Tabs Navigation -->
    <div class="bg-white rounded-lg shadow-sm border mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="showTab('basic')" id="tab-basic" class="tab-button {{ session('active_tab', 'basic') == 'basic' ? 'active' : '' }} py-4 px-1 border-b-2 {{ session('active_tab', 'basic') == 'basic' ? 'border-pink-500 font-medium text-sm text-pink-600' : 'border-transparent font-medium text-sm text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-user mr-2"></i>{{ __('messages.profile.basic_info') }}
                </button>
                <button onclick="showTab('details')" id="tab-details" class="tab-button {{ session('active_tab', 'basic') == 'details' ? 'active' : '' }} py-4 px-1 border-b-2 {{ session('active_tab', 'basic') == 'details' ? 'border-pink-500 font-medium text-sm text-pink-600' : 'border-transparent font-medium text-sm text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-info-circle mr-2"></i>{{ __('messages.profile.details') }}
                </button>
                <button onclick="showTab('photos')" id="tab-photos" class="tab-button {{ session('active_tab', 'basic') == 'photos' ? 'active' : '' }} py-4 px-1 border-b-2 {{ session('active_tab', 'basic') == 'photos' ? 'border-pink-500 font-medium text-sm text-pink-600' : 'border-transparent font-medium text-sm text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-images mr-2"></i>{{ __('messages.profile.photos') }}
                </button>
                <button onclick="showTab('privacy')" id="tab-privacy" class="tab-button {{ session('active_tab', 'basic') == 'privacy' ? 'active' : '' }} py-4 px-1 border-b-2 {{ session('active_tab', 'basic') == 'privacy' ? 'border-pink-500 font-medium text-sm text-pink-600' : 'border-transparent font-medium text-sm text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-shield-alt mr-2"></i>{{ __('messages.profile.privacy') }}
                </button>
            </nav>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="space-y-6">
        <!-- Basic Information Tab -->
        <div id="content-basic" class="tab-content">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">{{ __('messages.profile.basic_info') }}</h2>
                
                <!-- Foto de Perfil -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('messages.profile.profile_photo') }}</h3>
                    <div class="flex items-center space-x-6">
                        <!-- Foto atual -->
                        <div class="flex-shrink-0">
                            @if($user->profile_photo)
                                <img src="{{ Storage::url($user->profile_photo) }}" 
                                     alt="Foto de perfil" 
                                     class="w-20 h-20 rounded-full object-cover border-2 border-gray-300">
                            @else
                                <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center border-2 border-gray-300">
                                    <i class="fas fa-user text-gray-400 text-2xl"></i>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Upload -->
                        <div class="flex-1">
                            <form method="POST" action="{{ route('profile.update.photo') }}" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="profile_photo" class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ __('messages.profile.change_profile_photo') }}
                                    </label>
                                    <input type="file" 
                                           id="profile_photo" 
                                           name="profile_photo" 
                                           accept="image/*"
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100"
                                           data-label="{{ __('messages.profile.choose_file') }}"
                                           data-no-file="{{ __('messages.profile.no_file_chosen') }}">
                                    <p class="mt-1 text-xs text-gray-500">{{ __('messages.profile.photo_recommendation') }}</p>
                                    @error('profile_photo')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="flex space-x-3">
                                    <button type="submit" class="bg-pink-600 text-white px-4 py-2 rounded-lg hover:bg-pink-700 transition duration-200 text-sm">
                                        <i class="fas fa-upload mr-2"></i>{{ __('messages.profile.update_photo') }}
                                    </button>
                                    
                                    @if($user->profile_photo)
                                    <button type="button" 
                                            onclick="if(confirm('{{ __('messages.profile.confirm_remove_photo') }}')) { document.getElementById('remove-photo-form').submit(); }"
                                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-200 text-sm">
                                        <i class="fas fa-trash mr-2"></i>{{ __('messages.profile.remove') }}
                                    </button>
                                    @endif
                                </div>
                            </form>
                            
                            @if($user->profile_photo)
                            <form id="remove-photo-form" method="POST" action="{{ route('profile.remove.photo') }}" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('profile.update.basic') }}" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.profile.first_name') }} *</label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" required>
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.profile.last_name') }} *</label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" required>
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.profile.birth_date') }} *</label>
                            
                            @php
                                $birthDate = old('birth_date', $user->birth_date?->format('Y-m-d'));
                                $day = $birthDate ? date('d', strtotime($birthDate)) : '';
                                $month = $birthDate ? date('m', strtotime($birthDate)) : '';
                                $year = $birthDate ? date('Y', strtotime($birthDate)) : '';
                            @endphp
                            
                            <div id="birth_date_dropdown" class="grid grid-cols-3 gap-2">
                                <div>
                                    <label for="birth_day" class="sr-only">{{ __('messages.profile.day') }}</label>
                                    <select id="birth_day" 
                                            name="birth_day"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent text-sm">
                                        <option value="">{{ __('messages.profile.day') }}</option>
                                        @for($d = 1; $d <= 31; $d++)
                                            <option value="{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}" {{ old('birth_day', $day) == $d ? 'selected' : '' }}>{{ $d }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <label for="birth_month" class="sr-only">{{ __('messages.profile.month') }}</label>
                                    <select id="birth_month" 
                                            name="birth_month"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent text-sm">
                                        <option value="">{{ __('messages.profile.month') }}</option>
                                        @for($m = 1; $m <= 12; $m++)
                                            <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ old('birth_month', $month) == $m ? 'selected' : '' }}>{{ $m }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <label for="birth_year" class="sr-only">{{ __('messages.profile.year') }}</label>
                                    <select id="birth_year" 
                                            name="birth_year"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent text-sm">
                                        <option value="">{{ __('messages.profile.year') }}</option>
                                        @for($y = date('Y') - 18; $y >= 1920; $y--)
                                            <option value="{{ $y }}" {{ old('birth_year', $year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            
                            <input type="hidden" 
                                   id="birth_date" 
                                   name="birth_date" 
                                   value="{{ $birthDate }}"
                                   required>
                            
                            <p class="mt-1 text-xs text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                {{ __('messages.profile.age_requirement') }}
                            </p>
                            
                            @error('birth_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.profile.gender') }} *</label>
                            <select id="gender" name="gender" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" required>
                                <option value="">{{ __('messages.profile.select_option') }}</option>
                                <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>{{ __('messages.register.gender_male') }}</option>
                                <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>{{ __('messages.register.gender_female') }}</option>
                                <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>{{ __('messages.register.gender_other') }}</option>
                                <option value="prefer_not_to_say" {{ old('gender', $user->gender) == 'prefer_not_to_say' ? 'selected' : '' }}>{{ __('messages.register.gender_prefer_not_to_say') }}</option>
                            </select>
                            @error('gender')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>


                    <div class="flex justify-end">
                        <button type="submit" class="bg-pink-600 text-white px-6 py-2 rounded-lg hover:bg-pink-700 transition duration-200">
                            <i class="fas fa-save mr-2"></i>{{ __('messages.profile.save_basic_info') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Profile Details Tab -->
        <div id="content-details" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">{{ __('messages.profile.details') }}</h2>
                
                <form method="POST" action="{{ route('profile.update.details') }}" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.profile.bio') }}</label>
                        <textarea id="bio" name="bio" rows="4" 
                                  placeholder="{{ __('messages.profile.tell_about_yourself') }}" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">{{ old('bio', $user->profile?->bio) }}</textarea>
                        @error('bio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="occupation" class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.profile.occupation') }}</label>
                            <input type="text" id="occupation" name="occupation" value="{{ old('occupation', $user->profile?->occupation) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                            @error('occupation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="education_level" class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.profile.education_level') }}</label>
                            <select id="education_level" name="education_level" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                <option value="">{{ __('messages.profile.select_option') }}</option>
                                <option value="high_school" {{ old('education_level', $user->profile?->education_level) == 'high_school' ? 'selected' : '' }}>{{ __('messages.profile.high_school') }}</option>
                                <option value="bachelor" {{ old('education_level', $user->profile?->education_level) == 'bachelor' ? 'selected' : '' }}>{{ __('messages.profile.bachelor') }}</option>
                                <option value="master" {{ old('education_level', $user->profile?->education_level) == 'master' ? 'selected' : '' }}>{{ __('messages.profile.master') }}</option>
                                <option value="phd" {{ old('education_level', $user->profile?->education_level) == 'phd' ? 'selected' : '' }}>{{ __('messages.profile.phd') }}</option>
                                <option value="other" {{ old('education_level', $user->profile?->education_level) == 'other' ? 'selected' : '' }}>{{ __('messages.profile.other') }}</option>
                            </select>
                            @error('education_level')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="relationship_goal" class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.profile.relationship_goal') }}</label>
                        <select id="relationship_goal" name="relationship_goal" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                            <option value="">{{ __('messages.profile.select_option') }}</option>
                            <option value="friendship" {{ old('relationship_goal', $user->profile?->relationship_goal) == 'friendship' ? 'selected' : '' }}>{{ __('messages.profile.friendship') }}</option>
                            <option value="romance" {{ old('relationship_goal', $user->profile?->relationship_goal) == 'romance' ? 'selected' : '' }}>{{ __('messages.profile.romance') }}</option>
                            <option value="casual" {{ old('relationship_goal', $user->profile?->relationship_goal) == 'casual' ? 'selected' : '' }}>{{ __('messages.profile.casual') }}</option>
                            <option value="serious" {{ old('relationship_goal', $user->profile?->relationship_goal) == 'serious' ? 'selected' : '' }}>{{ __('messages.profile.serious_relationship') }}</option>
                            <option value="marriage" {{ old('relationship_goal', $user->profile?->relationship_goal) == 'marriage' ? 'selected' : '' }}>{{ __('messages.profile.marriage') }}</option>
                        </select>
                        @error('relationship_goal')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="smoking" class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.profile.smoking') }}</label>
                            <select id="smoking" name="smoking" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                <option value="">{{ __('messages.profile.select_option') }}</option>
                                <option value="never" {{ old('smoking', $user->profile?->smoking) == 'never' ? 'selected' : '' }}>{{ __('messages.profile.never') }}</option>
                                <option value="occasionally" {{ old('smoking', $user->profile?->smoking) == 'occasionally' ? 'selected' : '' }}>{{ __('messages.profile.occasionally') }}</option>
                                <option value="regularly" {{ old('smoking', $user->profile?->smoking) == 'regularly' ? 'selected' : '' }}>{{ __('messages.profile.regularly') }}</option>
                                <option value="prefer_not_to_say" {{ old('smoking', $user->profile?->smoking) == 'prefer_not_to_say' ? 'selected' : '' }}>{{ __('messages.profile.prefer_not_to_say') }}</option>
                            </select>
                            @error('smoking')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="drinking" class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.profile.drinking') }}</label>
                            <select id="drinking" name="drinking" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                <option value="">{{ __('messages.profile.select_option') }}</option>
                                <option value="never" {{ old('drinking', $user->profile?->drinking) == 'never' ? 'selected' : '' }}>{{ __('messages.profile.never') }}</option>
                                <option value="occasionally" {{ old('drinking', $user->profile?->drinking) == 'occasionally' ? 'selected' : '' }}>{{ __('messages.profile.occasionally') }}</option>
                                <option value="regularly" {{ old('drinking', $user->profile?->drinking) == 'regularly' ? 'selected' : '' }}>{{ __('messages.profile.regularly') }}</option>
                                <option value="prefer_not_to_say" {{ old('drinking', $user->profile?->drinking) == 'prefer_not_to_say' ? 'selected' : '' }}>{{ __('messages.profile.prefer_not_to_say') }}</option>
                            </select>
                            @error('drinking')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Children -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="has_children" class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.profile.has_children') }}</label>
                            <select id="has_children" name="has_children" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                <option value="">{{ __('messages.profile.select_option') }}</option>
                                <option value="yes" {{ old('has_children', $user->profile?->has_children) == 'yes' ? 'selected' : '' }}>{{ __('messages.profile.yes') }}</option>
                                <option value="no" {{ old('has_children', $user->profile?->has_children) == 'no' ? 'selected' : '' }}>{{ __('messages.profile.no') }}</option>
                                <option value="prefer_not_to_say" {{ old('has_children', $user->profile?->has_children) == 'prefer_not_to_say' ? 'selected' : '' }}>{{ __('messages.profile.prefer_not_to_say') }}</option>
                            </select>
                        </div>

                        <div>
                            <label for="wants_children" class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.profile.wants_children') }}</label>
                            <select id="wants_children" name="wants_children" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                <option value="">{{ __('messages.profile.select_option') }}</option>
                                <option value="yes" {{ old('wants_children', $user->profile?->wants_children) == 'yes' ? 'selected' : '' }}>{{ __('messages.profile.yes') }}</option>
                                <option value="no" {{ old('wants_children', $user->profile?->wants_children) == 'no' ? 'selected' : '' }}>{{ __('messages.profile.no') }}</option>
                                <option value="maybe" {{ old('wants_children', $user->profile?->wants_children) == 'maybe' ? 'selected' : '' }}>{{ __('messages.profile.maybe') }}</option>
                                <option value="prefer_not_to_say" {{ old('wants_children', $user->profile?->wants_children) == 'prefer_not_to_say' ? 'selected' : '' }}>{{ __('messages.profile.prefer_not_to_say') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Physical Characteristics -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="body_type" class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.profile.body_type') }}</label>
                            <select id="body_type" name="body_type" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                <option value="">{{ __('messages.profile.select_option') }}</option>
                                <option value="slim" {{ old('body_type', $user->profile?->body_type) == 'slim' ? 'selected' : '' }}>{{ __('messages.profile.body_type_slim') }}</option>
                                <option value="athletic" {{ old('body_type', $user->profile?->body_type) == 'athletic' ? 'selected' : '' }}>{{ __('messages.profile.body_type_athletic') }}</option>
                                <option value="average" {{ old('body_type', $user->profile?->body_type) == 'average' ? 'selected' : '' }}>{{ __('messages.profile.body_type_average') }}</option>
                                <option value="curvy" {{ old('body_type', $user->profile?->body_type) == 'curvy' ? 'selected' : '' }}>{{ __('messages.profile.body_type_curvy') }}</option>
                                <option value="plus_size" {{ old('body_type', $user->profile?->body_type) == 'plus_size' ? 'selected' : '' }}>{{ __('messages.profile.body_type_plus_size') }}</option>
                                <option value="muscular" {{ old('body_type', $user->profile?->body_type) == 'muscular' ? 'selected' : '' }}>{{ __('messages.profile.body_type_muscular') }}</option>
                                <option value="prefer_not_to_say" {{ old('body_type', $user->profile?->body_type) == 'prefer_not_to_say' ? 'selected' : '' }}>{{ __('messages.profile.prefer_not_to_say') }}</option>
                            </select>
                        </div>

                        <div>
                            <label for="height" class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.profile.height') }}</label>
                            <input type="number" id="height" name="height" value="{{ old('height', $user->profile?->height) }}" 
                                   placeholder="{{ __('messages.profile.height_placeholder') }}" 
                                   min="100" max="250"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.profile.weight') }}</label>
                            <input type="number" id="weight" name="weight" value="{{ old('weight', $user->profile?->weight) }}" 
                                   placeholder="{{ __('messages.profile.weight_placeholder') }}" 
                                   min="30" max="200"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                        </div>
                    </div>

                    <!-- Lifestyle -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="diet_type" class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.profile.diet_type') }}</label>
                            <select id="diet_type" name="diet_type" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                <option value="">{{ __('messages.profile.select_option') }}</option>
                                <option value="omnivore" {{ old('diet_type', $user->profile?->diet_type) == 'omnivore' ? 'selected' : '' }}>{{ __('messages.profile.diet_omnivore') }}</option>
                                <option value="vegetarian" {{ old('diet_type', $user->profile?->diet_type) == 'vegetarian' ? 'selected' : '' }}>{{ __('messages.profile.diet_vegetarian') }}</option>
                                <option value="vegan" {{ old('diet_type', $user->profile?->diet_type) == 'vegan' ? 'selected' : '' }}>{{ __('messages.profile.diet_vegan') }}</option>
                                <option value="pescatarian" {{ old('diet_type', $user->profile?->diet_type) == 'pescatarian' ? 'selected' : '' }}>{{ __('messages.profile.diet_pescatarian') }}</option>
                                <option value="keto" {{ old('diet_type', $user->profile?->diet_type) == 'keto' ? 'selected' : '' }}>{{ __('messages.profile.diet_keto') }}</option>
                                <option value="paleo" {{ old('diet_type', $user->profile?->diet_type) == 'paleo' ? 'selected' : '' }}>{{ __('messages.profile.diet_paleo') }}</option>
                                <option value="other" {{ old('diet_type', $user->profile?->diet_type) == 'other' ? 'selected' : '' }}>{{ __('messages.profile.other') }}</option>
                                <option value="prefer_not_to_say" {{ old('diet_type', $user->profile?->diet_type) == 'prefer_not_to_say' ? 'selected' : '' }}>{{ __('messages.profile.prefer_not_to_say') }}</option>
                            </select>
                        </div>

                        <div>
                            <label for="exercise_frequency" class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.profile.exercise_frequency') }}</label>
                            <select id="exercise_frequency" name="exercise_frequency" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                <option value="">{{ __('messages.profile.select_option') }}</option>
                                <option value="daily" {{ old('exercise_frequency', $user->profile?->exercise_frequency) == 'daily' ? 'selected' : '' }}>{{ __('messages.profile.exercise_daily') }}</option>
                                <option value="several_times_week" {{ old('exercise_frequency', $user->profile?->exercise_frequency) == 'several_times_week' ? 'selected' : '' }}>{{ __('messages.profile.exercise_several_times') }}</option>
                                <option value="weekly" {{ old('exercise_frequency', $user->profile?->exercise_frequency) == 'weekly' ? 'selected' : '' }}>{{ __('messages.profile.exercise_weekly') }}</option>
                                <option value="monthly" {{ old('exercise_frequency', $user->profile?->exercise_frequency) == 'monthly' ? 'selected' : '' }}>{{ __('messages.profile.exercise_monthly') }}</option>
                                <option value="rarely" {{ old('exercise_frequency', $user->profile?->exercise_frequency) == 'rarely' ? 'selected' : '' }}>{{ __('messages.profile.exercise_rarely') }}</option>
                                <option value="never" {{ old('exercise_frequency', $user->profile?->exercise_frequency) == 'never' ? 'selected' : '' }}>{{ __('messages.profile.exercise_never') }}</option>
                                <option value="prefer_not_to_say" {{ old('exercise_frequency', $user->profile?->exercise_frequency) == 'prefer_not_to_say' ? 'selected' : '' }}>{{ __('messages.profile.prefer_not_to_say') }}</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="looking_for" class="block text-sm font-medium text-gray-700 mb-2">{{ __('messages.profile.looking_for') }}</label>
                        <textarea id="looking_for" name="looking_for" rows="3" 
                                  placeholder="{{ __('messages.profile.describe_what_you_are_looking_for') }}" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">{{ old('looking_for', $user->profile?->looking_for) }}</textarea>
                        @error('looking_for')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-pink-600 text-white px-6 py-2 rounded-lg hover:bg-pink-700 transition duration-200">
                            <i class="fas fa-save mr-2"></i>{{ __('messages.profile.save_details') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Photos Tab -->
        <div id="content-photos" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">{{ __('messages.profile.manage_photos') }}</h2>
                
                <!-- Upload New Photo -->
                <div class="mb-6 p-4 border-2 border-dashed border-gray-300 rounded-lg">
                    <form method="POST" action="{{ route('photos.store') }}" enctype="multipart/form-data" class="text-center">
                        @csrf
                        <label for="photo" class="cursor-pointer block">
                            <div class="space-y-4">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400"></i>
                                <div>
                                    <span class="mt-2 block text-sm font-medium text-gray-900">
                                        {{ __('messages.profile.click_to_add_photo') }}
                                    </span>
                                    <span class="mt-1 block text-sm text-gray-500">
                                        {{ __('messages.profile.photo_recommendation_upload') }}
                                    </span>
                                </div>
                            </div>
                        </label>
                        <input type="file" id="photo" name="photo" accept="image/*" class="hidden" onchange="this.form.submit()">
                    </form>
                </div>

                <!-- Current Photos -->
                @if($user->photos->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($user->photos as $photo)
                    <div class="relative group">
                        <img src="{{ Storage::url($photo->photo_path) }}" 
                             alt="Foto do perfil" 
                             class="w-full h-48 object-cover rounded-lg">
                        
                        @if($photo->is_primary)
                        <div class="absolute top-2 left-2 bg-pink-600 text-white px-2 py-1 rounded text-xs font-medium">
                            {{ __('messages.profile.primary') }}
                        </div>
                        @endif

                        @if($photo->moderation_status === 'pending')
                        <div class="absolute top-2 right-2 bg-yellow-500 text-white px-2 py-1 rounded text-xs font-medium">
                            {{ __('messages.profile.pending') }}
                        </div>
                        @endif

                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition duration-200 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                            <div class="flex space-x-2">
                                @if(!$photo->is_primary)
                                <form method="POST" action="{{ route('photos.primary', $photo) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-pink-600 text-white p-2 rounded-full hover:bg-pink-700 transition duration-200" title="{{ __('messages.profile.set_as_primary') }}">
                                        <i class="fas fa-star"></i>
                                    </button>
                                </form>
                                @endif
                                
                                <form method="POST" action="{{ route('photos.destroy', $photo) }}" class="inline" onsubmit="return confirm('{{ __('messages.profile.confirm_delete_photo') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700 transition duration-200" title="{{ __('messages.profile.delete_photo') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-images text-4xl mb-4"></i>
                    <p>{{ __('messages.profile.no_photos_added') }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Privacy Tab -->
        <div id="content-privacy" class="tab-content hidden">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">{{ __('messages.profile.privacy_settings') }}</h2>
                
                <form method="POST" action="{{ route('profile.update.privacy') }}" class="space-y-6">
                    @csrf
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">{{ __('messages.profile.show_distance') }}</h3>
                                <p class="text-sm text-gray-500">{{ __('messages.profile.show_distance_description') }}</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="show_distance" value="1" 
                                       {{ old('show_distance', $user->profile?->show_distance ?? true) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">{{ __('messages.profile.show_age') }}</h3>
                                <p class="text-sm text-gray-500">{{ __('messages.profile.show_age_description') }}</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="show_age" value="1" 
                                       {{ old('show_age', $user->profile?->show_age ?? true) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">{{ __('messages.profile.show_online_status') }}</h3>
                                <p class="text-sm text-gray-500">{{ __('messages.profile.show_online_status_description') }}</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="show_online_status" value="1" 
                                       {{ old('show_online_status', $user->profile?->show_online_status ?? true) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-pink-600 text-white px-6 py-2 rounded-lg hover:bg-pink-700 transition duration-200">
                            <i class="fas fa-save mr-2"></i>{{ __('messages.profile.save_settings') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tab contents
    const contents = document.querySelectorAll('.tab-content');
    contents.forEach(content => content.classList.add('hidden'));
    
    // Remove active class from all tab buttons
    const buttons = document.querySelectorAll('.tab-button');
    buttons.forEach(button => {
        button.classList.remove('active', 'border-pink-500', 'text-pink-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Add active class to selected tab button
    const activeButton = document.getElementById('tab-' + tabName);
    activeButton.classList.add('active', 'border-pink-500', 'text-pink-600');
    activeButton.classList.remove('border-transparent', 'text-gray-500');
}

// Show the correct tab on page load based on session
document.addEventListener('DOMContentLoaded', function() {
    const activeTab = '{{ session("active_tab", "basic") }}';
    showTab(activeTab);
    
    // Birth date dropdowns functionality
    const birthDay = document.getElementById('birth_day');
    const birthMonth = document.getElementById('birth_month');
    const birthYear = document.getElementById('birth_year');
    const birthDate = document.getElementById('birth_date');

    if (birthDay && birthMonth && birthYear && birthDate) {
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
                    birthDate.setCustomValidity('{{ __('messages.profile.invalid_date') }}');
                }
            } else {
                birthDate.value = '';
                birthDate.setCustomValidity('');
            }
        }

        birthDay.addEventListener('change', updateBirthDate);
        birthMonth.addEventListener('change', updateBirthDate);
        birthYear.addEventListener('change', updateBirthDate);
    }
});

// Update file input label
document.getElementById('profile_photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        this.value = file.name;
    }
});
</script>
@endsection
