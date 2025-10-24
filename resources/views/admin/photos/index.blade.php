@extends('layouts.app')

@section('title', 'Moderação de Fotos - Admin')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-images text-blue-500 mr-3"></i>
                        Moderação de Fotos
                    </h1>
                    <p class="mt-2 text-gray-600">Gerencie e modere fotos enviadas pelos usuários</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('admin.photos.statistics') }}" 
                       class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-200">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Estatísticas
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pendentes</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $photos->total() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Aprovadas</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\UserPhoto::where('moderation_status', 'approved')->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-times text-red-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Rejeitadas</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\UserPhoto::where('moderation_status', 'rejected')->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-images text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\UserPhoto::count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow mb-6 p-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Usuário</label>
                    <select name="user_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Todos os usuários</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data Inicial</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data Final</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-search mr-2"></i>
                        Filtrar
                    </button>
                </div>
            </form>
        </div>

        <!-- Bulk Actions -->
        @if($photos->count() > 0)
        <div class="bg-white rounded-lg shadow mb-6 p-4">
            <form id="bulkForm" method="POST" action="{{ route('admin.photos.bulk-approve') }}">
                @csrf
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <button type="button" id="selectAll" class="text-blue-600 hover:text-blue-800 font-medium">
                            Selecionar Todas
                        </button>
                        <span id="selectedCount" class="text-sm text-gray-500">0 selecionadas</span>
                    </div>
                    <div class="flex space-x-2">
                        <button type="button" id="bulkApproveBtn" 
                                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                            <i class="fas fa-check mr-2"></i>
                            Aprovar Selecionadas
                        </button>
                        <button type="button" id="bulkRejectBtn" 
                                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                            <i class="fas fa-times mr-2"></i>
                            Rejeitar Selecionadas
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @endif

        <!-- Photos Grid -->
        <div class="bg-white rounded-lg shadow">
            @if($photos->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 p-6">
                    @foreach($photos as $photo)
                        <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition duration-200">
                            <!-- Photo -->
                            <div class="aspect-square bg-gray-100 relative">
                                <img src="{{ Storage::url($photo->file_path) }}" 
                                     alt="Foto do usuário {{ $photo->user->name }}"
                                     class="w-full h-full object-cover">
                                <div class="absolute top-2 right-2">
                                    <input type="checkbox" name="photo_ids[]" value="{{ $photo->id }}" 
                                           class="photo-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </div>
                            </div>
                            
                            <!-- Photo Info -->
                            <div class="p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="font-medium text-gray-900 truncate">{{ $photo->user->name }}</h3>
                                    <span class="text-xs text-gray-500">{{ $photo->created_at->format('d/m/Y') }}</span>
                                </div>
                                
                                <p class="text-sm text-gray-600 mb-3">{{ $photo->user->email }}</p>
                                
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.photos.show', $photo) }}" 
                                       class="flex-1 bg-blue-600 text-white text-center px-3 py-2 rounded text-sm hover:bg-blue-700 transition duration-200">
                                        <i class="fas fa-eye mr-1"></i>
                                        Ver
                                    </a>
                                    <form method="POST" action="{{ route('admin.photos.approve', $photo) }}" class="flex-1">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full bg-green-600 text-white px-3 py-2 rounded text-sm hover:bg-green-700 transition duration-200">
                                            <i class="fas fa-check mr-1"></i>
                                            Aprovar
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.photos.reject', $photo) }}" class="flex-1">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full bg-red-600 text-white px-3 py-2 rounded text-sm hover:bg-red-700 transition duration-200">
                                            <i class="fas fa-times mr-1"></i>
                                            Rejeitar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $photos->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-images text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhuma foto pendente</h3>
                    <p class="text-gray-500">Todas as fotos foram moderadas ou não há fotos para moderar.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Bulk Reject Modal -->
<div id="bulkRejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Rejeitar Fotos Selecionadas</h3>
            <form id="bulkRejectForm" method="POST" action="{{ route('admin.photos.bulk-reject') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Motivo da Rejeição</label>
                    <select name="reason" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Selecione um motivo</option>
                        <option value="inappropriate">Conteúdo inadequado</option>
                        <option value="low_quality">Baixa qualidade</option>
                        <option value="not_clear">Imagem não clara</option>
                        <option value="other">Outro</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Observações</label>
                    <textarea name="moderation_notes" required rows="3" 
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Explique o motivo da rejeição..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelBulkReject" 
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-200">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200">
                        Rejeitar Fotos
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.photo-checkbox');
    const selectAllBtn = document.getElementById('selectAll');
    const selectedCount = document.getElementById('selectedCount');
    const bulkApproveBtn = document.getElementById('bulkApproveBtn');
    const bulkRejectBtn = document.getElementById('bulkRejectBtn');
    const bulkForm = document.getElementById('bulkForm');
    const bulkRejectModal = document.getElementById('bulkRejectModal');
    const bulkRejectForm = document.getElementById('bulkRejectForm');
    const cancelBulkReject = document.getElementById('cancelBulkReject');

    function updateSelectedCount() {
        const selected = document.querySelectorAll('.photo-checkbox:checked');
        selectedCount.textContent = `${selected.length} selecionadas`;
        bulkApproveBtn.disabled = selected.length === 0;
        bulkRejectBtn.disabled = selected.length === 0;
    }

    function updateBulkForm() {
        const selected = document.querySelectorAll('.photo-checkbox:checked');
        const photoIds = Array.from(selected).map(cb => cb.value);
        
        // Clear existing hidden inputs
        const existingInputs = bulkForm.querySelectorAll('input[name="photo_ids[]"]');
        existingInputs.forEach(input => input.remove());
        
        // Add new hidden inputs
        photoIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'photo_ids[]';
            input.value = id;
            bulkForm.appendChild(input);
        });
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectedCount();
            updateBulkForm();
        });
    });

    selectAllBtn.addEventListener('click', function() {
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });
        updateSelectedCount();
        updateBulkForm();
        selectAllBtn.textContent = allChecked ? 'Selecionar Todas' : 'Desselecionar Todas';
    });

    bulkApproveBtn.addEventListener('click', function() {
        if (confirm('Tem certeza que deseja aprovar as fotos selecionadas?')) {
            bulkForm.submit();
        }
    });

    bulkRejectBtn.addEventListener('click', function() {
        updateBulkForm();
        bulkRejectModal.classList.remove('hidden');
    });

    cancelBulkReject.addEventListener('click', function() {
        bulkRejectModal.classList.add('hidden');
    });

    bulkRejectForm.addEventListener('submit', function() {
        updateBulkForm();
    });
});
</script>
@endsection
