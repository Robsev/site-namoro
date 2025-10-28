@extends('layouts.admin')

@section('title', 'Detalhes da Denúncia - Admin')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Denúncia #{{ $report->id }}</h2>
            <p class="mt-2 text-gray-600">Detalhes e ações administrativas</p>
        </div>
        <div class="space-x-2">
            <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('messages.admin.photos.back') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Report Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="border rounded-lg p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Informações</h3>
                <p class="text-sm text-gray-600 mb-2">Criada em {{ $report->created_at->format('d/m/Y H:i') }}</p>
                <p class="text-sm text-gray-600 mb-4">
                    Status: 
                    @php
                        $badge = match($report->status) {
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'reviewed' => 'bg-blue-100 text-blue-800',
                            'resolved' => 'bg-green-100 text-green-800',
                            'dismissed' => 'bg-gray-100 text-gray-800',
                            default => 'bg-gray-100 text-gray-800'
                        };
                    @endphp
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badge }}">
                        {{ ucfirst($report->status) }}
                    </span>
                </p>
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-1">Motivo</h4>
                    <p class="text-gray-800 whitespace-pre-line">{{ $report->reason }}</p>
                </div>
            </div>

            <div class="border rounded-lg p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Ações</h3>
                <form method="POST" action="{{ route('admin.reports.update-status', $report) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Atualizar Status</label>
                        <select name="status" class="w-full rounded border-gray-300">
                            @foreach(['pending' => 'Pendente', 'reviewed' => 'Revisada', 'resolved' => 'Resolvida', 'dismissed' => 'Descartada'] as $value => $label)
                                <option value="{{ $value }}" @if($report->status===$value) selected @endif>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Anotações do Admin</label>
                        <textarea name="admin_notes" rows="4" class="w-full rounded border-gray-300" placeholder="Adicione informações relevantes...">{{ old('admin_notes', $report->admin_notes) }}</textarea>
                    </div>
                    <div>
                        <button class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-900">Salvar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- User Cards -->
        <div class="space-y-6">
            <div class="border rounded-lg p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Denunciante</h3>
                <p class="text-sm text-gray-900">{{ $report->reporter->name }}</p>
                <p class="text-xs text-gray-500 mb-2">{{ $report->reporter->email }}</p>
                <p class="text-xs text-gray-500">Membro desde {{ $report->reporter->created_at->format('M/Y') }}</p>
            </div>

            <div class="border rounded-lg p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Denunciado</h3>
                <p class="text-sm text-gray-900">{{ $report->reportedUser->name }}</p>
                <p class="text-xs text-gray-500 mb-2">{{ $report->reportedUser->email }}</p>
                <p class="text-xs text-gray-500">Membro desde {{ $report->reportedUser->created_at->format('M/Y') }}</p>

                <div class="mt-4 space-x-2">
                    @if($report->reportedUser->is_active)
                        <form method="POST" action="{{ route('admin.reports.deactivate-user', $report) }}" class="inline">
                            @csrf
                            <button class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                <i class="fas fa-user-slash mr-1"></i>Desativar Conta
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.reports.activate-user', $report) }}" class="inline">
                            @csrf
                            <button class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                <i class="fas fa-user-check mr-1"></i>Reativar Conta
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
