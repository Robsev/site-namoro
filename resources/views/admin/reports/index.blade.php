@extends('layouts.admin')

@section('title', 'Denúncias - Admin')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ __('messages.admin.reports') }}</h2>
            <p class="mt-2 text-gray-600">Gerencie e revise denúncias de usuários</p>
        </div>
        <div>
            <a href="{{ route('admin.photos.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                <i class="fas fa-images mr-2"></i>{{ __('messages.admin.photos') }}
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg">
            <p class="text-sm font-medium text-gray-500">Pendentes</p>
            <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg">
            <p class="text-sm font-medium text-gray-500">Revisadas</p>
            <p class="text-2xl font-semibold text-gray-900">{{ $stats['reviewed'] }}</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg">
            <p class="text-sm font-medium text-gray-500">Resolvidas</p>
            <p class="text-2xl font-semibold text-gray-900">{{ $stats['resolved'] }}</p>
        </div>
        <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-sm font-medium text-gray-500">Descartadas</p>
            <p class="text-2xl font-semibold text-gray-900">{{ $stats['dismissed'] }}</p>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg">
            <p class="text-sm font-medium text-gray-500">Total</p>
            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="w-full rounded border-gray-300">
                <option value="">Todos</option>
                @foreach(['pending' => 'Pendentes', 'reviewed' => 'Revisadas', 'resolved' => 'Resolvidas', 'dismissed' => 'Descartadas'] as $value => $label)
                    <option value="{{ $value }}" @if(request('status')===$value) selected @endif>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome, e-mail ou motivo" class="w-full rounded border-gray-300" />
        </div>
        <div class="md:col-span-3">
            <button class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-900">Filtrar</button>
        </div>
    </form>

    <!-- Reports List -->
    @if($reports->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Denunciante</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Denunciado</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Motivo</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($reports as $report)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900">#{{ $report->id }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $report->reporter->name }}<br>
                                <span class="text-gray-500 text-xs">{{ $report->reporter->email }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $report->reportedUser->name }}<br>
                                <span class="text-gray-500 text-xs">{{ $report->reportedUser->email }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 truncate max-w-md">{{ \Str::limit($report->reason, 100) }}</td>
                            <td class="px-4 py-3">
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
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.reports.show', $report) }}" class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                                    <i class="fas fa-eye mr-2"></i>Ver
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $reports->withQueryString()->links() }}</div>
    @else
        <div class="text-center py-12">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhuma denúncia encontrada</h3>
            <p class="text-gray-500">Quando houver denúncias de usuários, elas aparecerão aqui.</p>
        </div>
    @endif
</div>
@endsection
