@extends('layouts.profile')

@section('title', 'Meus Interesses')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Meus Interesses</h1>
        <p class="mt-2 text-gray-600">Selecione seus interesses para encontrar pessoas com gostos similares.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('interests.update') }}" class="space-y-8">
        @csrf
        
        @foreach($categories as $category)
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-{{ $category->slug === 'music' ? 'music' : ($category->slug === 'sports' ? 'dumbbell' : ($category->slug === 'literature' ? 'book' : ($category->slug === 'cinema_tv' ? 'tv' : ($category->slug === 'hobbies' ? 'puzzle-piece' : ($category->slug === 'travel' ? 'plane' : ($category->slug === 'food' ? 'utensils' : 'laptop')))))) }} text-pink-500 mr-3"></i>
                    {{ $category->name }}
                </h2>
                <p class="text-gray-600 text-sm">{{ $category->description }}</p>
                <p class="text-gray-500 text-xs mt-1">
                    Selecione até {{ $category->max_selections }} opções
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach($category->options as $option)
                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition duration-200 {{ in_array($option, $userInterests[$category->id] ?? []) ? 'bg-pink-50 border-pink-300' : '' }}">
                    <input type="checkbox" 
                           name="interests[{{ $category->id }}][]" 
                           value="{{ $option }}"
                           class="sr-only interest-checkbox"
                           data-category="{{ $category->id }}"
                           data-max="{{ $category->max_selections }}"
                           {{ in_array($option, $userInterests[$category->id] ?? []) ? 'checked' : '' }}>
                    <div class="flex-1 text-sm font-medium text-gray-700 text-center">
                        {{ $option }}
                    </div>
                </label>
                @endforeach
            </div>

            @error($category->slug)
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <div class="mt-4 text-sm text-gray-500">
                <span id="selected-count-{{ $category->id }}">0</span> de {{ $category->max_selections }} selecionados
            </div>
        </div>
        @endforeach

        <div class="flex justify-end">
            <button type="submit" class="bg-pink-600 text-white px-8 py-3 rounded-lg hover:bg-pink-700 transition duration-200 font-medium">
                <i class="fas fa-save mr-2"></i>Salvar Interesses
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar contadores
    document.querySelectorAll('[data-category]').forEach(checkbox => {
        updateCounter(checkbox.dataset.category);
    });

    // Adicionar event listeners
    document.querySelectorAll('.interest-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const categoryId = this.dataset.category;
            const maxSelections = parseInt(this.dataset.max);
            const checkedBoxes = document.querySelectorAll(`[data-category="${categoryId}"]:checked`);
            
            // Se excedeu o limite, desmarcar o último
            if (checkedBoxes.length > maxSelections) {
                this.checked = false;
                return;
            }
            
            // Atualizar visual
            const label = this.closest('label');
            if (this.checked) {
                label.classList.add('bg-pink-50', 'border-pink-300');
            } else {
                label.classList.remove('bg-pink-50', 'border-pink-300');
            }
            
            updateCounter(categoryId);
        });
    });

    function updateCounter(categoryId) {
        const checkedBoxes = document.querySelectorAll(`[data-category="${categoryId}"]:checked`);
        const counter = document.getElementById(`selected-count-${categoryId}`);
        if (counter) {
            counter.textContent = checkedBoxes.length;
        }
    }
});
</script>
@endsection
