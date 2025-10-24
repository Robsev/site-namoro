@extends('layouts.profile')

@section('title', 'Estatísticas de Moderação - Admin')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-chart-bar text-blue-500 mr-3"></i>
                        Estatísticas de Moderação
                    </h1>
                    <p class="mt-2 text-gray-600">Acompanhe o desempenho do sistema de moderação</p>
                </div>
                <a href="{{ route('admin.photos.index') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Voltar
                </a>
            </div>
        </div>

        <!-- Statistics Overview -->
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
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending'] }}</p>
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
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['approved'] }}</p>
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
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['rejected'] }}</p>
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
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Approval Rate Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-chart-pie text-green-500 mr-2"></i>
                    Taxa de Aprovação
                </h2>
                <div class="flex items-center justify-center">
                    <div class="relative w-48 h-48">
                        <svg class="w-48 h-48 transform -rotate-90" viewBox="0 0 100 100">
                            <!-- Background circle -->
                            <circle cx="50" cy="50" r="40" stroke="#e5e7eb" stroke-width="8" fill="none"/>
                            <!-- Progress circle -->
                            @php
                                $approvedRate = $stats['total'] > 0 ? ($stats['approved'] / $stats['total']) * 100 : 0;
                                $rejectedRate = $stats['total'] > 0 ? ($stats['rejected'] / $stats['total']) * 100 : 0;
                                $pendingRate = $stats['total'] > 0 ? ($stats['pending'] / $stats['total']) * 100 : 0;
                            @endphp
                            <circle cx="50" cy="50" r="40" 
                                    stroke="#10b981" 
                                    stroke-width="8" 
                                    fill="none"
                                    stroke-dasharray="{{ $approvedRate * 2.51 }} 251"
                                    stroke-dashoffset="0"/>
                            <circle cx="50" cy="50" r="40" 
                                    stroke="#ef4444" 
                                    stroke-width="8" 
                                    fill="none"
                                    stroke-dasharray="{{ $rejectedRate * 2.51 }} 251"
                                    stroke-dashoffset="{{ -$approvedRate * 2.51 }}"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">{{ number_format($approvedRate, 1) }}%</div>
                                <div class="text-sm text-gray-500">Aprovadas</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex justify-center space-x-6">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                        <span class="text-sm text-gray-600">Aprovadas ({{ $stats['approved'] }})</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                        <span class="text-sm text-gray-600">Rejeitadas ({{ $stats['rejected'] }})</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                        <span class="text-sm text-gray-600">Pendentes ({{ $stats['pending'] }})</span>
                    </div>
                </div>
            </div>

            <!-- Daily Activity Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-chart-line text-blue-500 mr-2"></i>
                    Atividade dos Últimos 7 Dias
                </h2>
                <div class="h-64 flex items-end justify-between space-x-2">
                    @for($i = 6; $i >= 0; $i--)
                        @php
                            $date = now()->subDays($i);
                            $dayStats = \App\Models\UserPhoto::whereDate('created_at', $date)->count();
                            $maxHeight = 100;
                            $height = $stats['total'] > 0 ? ($dayStats / max($stats['total'] / 7, 1)) * $maxHeight : 0;
                        @endphp
                        <div class="flex flex-col items-center">
                            <div class="w-8 bg-blue-500 rounded-t" style="height: {{ max($height, 4) }}px;"></div>
                            <div class="text-xs text-gray-500 mt-2">{{ $date->format('d/m') }}</div>
                            <div class="text-xs text-gray-400">{{ $dayStats }}</div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">
                    <i class="fas fa-history text-purple-500 mr-2"></i>
                    Atividade Recente
                </h2>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($recentActivity as $activity)
                    <div class="px-6 py-4 flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center
                                @if($activity->moderation_status === 'approved') bg-green-100 text-green-600
                                @else bg-red-100 text-red-600 @endif">
                                <i class="fas @if($activity->moderation_status === 'approved') fa-check @else fa-times @endif"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2">
                                <p class="text-sm font-medium text-gray-900">
                                    Foto {{ $activity->moderation_status === 'approved' ? 'aprovada' : 'rejeitada' }}
                                </p>
                                <span class="text-xs text-gray-500">
                                    {{ $activity->moderated_at->diffForHumans() }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">
                                Usuário: <span class="font-medium">{{ $activity->user->name }}</span>
                                @if($activity->moderator)
                                    • Moderada por: <span class="font-medium">{{ $activity->moderator->name }}</span>
                                @endif
                            </p>
                            @if($activity->moderation_notes)
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-comment mr-1"></i>
                                    {{ Str::limit($activity->moderation_notes, 100) }}
                                </p>
                            @endif
                        </div>
                        <div class="flex-shrink-0">
                            <a href="{{ route('admin.photos.show', $activity) }}" 
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Ver detalhes
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center">
                        <i class="fas fa-history text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">Nenhuma atividade recente encontrada.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
