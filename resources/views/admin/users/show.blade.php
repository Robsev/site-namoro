@extends('layouts.admin')

@section('title', 'Detalhes do Usuário - Admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                @if($user->profile_photo)
                    <img src="{{ $user->profile_photo_url }}" 
                         alt="{{ $user->name }}" 
                         class="h-16 w-16 rounded-full object-cover mr-4">
                @else
                    <div class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center mr-4">
                        <i class="fas fa-user text-gray-600 text-2xl"></i>
                    </div>
                @endif
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        {{ $user->name }}
                        @if($user->is_admin)
                            <span class="ml-2 px-2 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                                <i class="fas fa-crown"></i> Administrador
                            </span>
                        @endif
                    </h2>
                    <p class="text-gray-600">{{ $user->email }}</p>
                </div>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.users.index') }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Voltar
                </a>
                @if($user->is_active)
                    <form method="POST" action="{{ route('admin.users.deactivate', $user) }}" class="inline">
                        @csrf
                        <button type="submit" 
                                class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 transition duration-200"
                                onclick="return confirm('Tem certeza que deseja desativar este usuário?')">
                            <i class="fas fa-ban mr-2"></i>Desativar
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.users.activate', $user) }}" class="inline">
                        @csrf
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition duration-200">
                            <i class="fas fa-check mr-2"></i>Ativar
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Status Badges -->
        <div class="flex flex-wrap gap-2 mt-4">
            @if($user->is_active)
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                    <i class="fas fa-check-circle mr-1"></i> Ativo
                </span>
            @else
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                    <i class="fas fa-times-circle mr-1"></i> Inativo
                </span>
            @endif

            @if($user->email_verified_at)
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                    <i class="fas fa-envelope-check mr-1"></i> E-mail Verificado
                </span>
            @else
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                    <i class="fas fa-envelope-exclamation mr-1"></i> E-mail Não Verificado
                </span>
            @endif

            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-indigo-100 text-indigo-800">
                <i class="fas fa-star mr-1"></i> Premium
            </span>
        </div>
    </div>

    <!-- User Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Basic Information -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                Informações Básicas
            </h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">ID</dt>
                    <dd class="mt-1 text-sm text-gray-900">#{{ $user->id }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Nome Completo</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->full_name ?? $user->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">E-mail</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                </div>
                @if($user->phone)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Telefone</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->phone }}</dd>
                </div>
                @endif
                @if($user->birth_date)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Data de Nascimento</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->birth_date->format('d/m/Y') }} ({{ $user->age }} anos)</dd>
                </div>
                @endif
                @if($user->gender)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Gênero</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($user->gender) }}</dd>
                </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-gray-500">Localização</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->formatted_location ?? 'Não informado' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Idioma Preferido</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->preferred_language ?? 'Não definido' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Account Information -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-user-cog text-purple-500 mr-2"></i>
                Informações da Conta
            </h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Data de Cadastro</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d/m/Y H:i:s') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Última Atualização</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('d/m/Y H:i:s') }}</dd>
                </div>
                @if($user->email_verified_at)
                <div>
                    <dt class="text-sm font-medium text-gray-500">E-mail Verificado em</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->email_verified_at->format('d/m/Y H:i:s') }}</dd>
                </div>
                @endif
                @if($user->last_seen)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Última Visita</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user->last_seen->format('d/m/Y H:i:s') }} ({{ $user->last_seen->diffForHumans() }})</dd>
                </div>
                @endif
                @if($user->subscription_type)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tipo de Assinatura</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($user->subscription_type) }}</dd>
                </div>
                @endif
                @if($user->subscription_expires_at)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Assinatura Expira em</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $user->subscription_expires_at->format('d/m/Y H:i:s') }}
                        @if($user->subscription_expires_at->isFuture())
                            <span class="text-green-600">(Ativa)</span>
                        @else
                            <span class="text-red-600">(Expirada)</span>
                        @endif
                    </dd>
                </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-gray-500">Completude do Perfil</dt>
                    <dd class="mt-1">
                        <div class="flex items-center">
                            <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $user->profile_completeness }}%"></div>
                            </div>
                            <span class="text-sm text-gray-900">{{ $user->profile_completeness }}%</span>
                        </div>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Statistics -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-chart-bar text-green-500 mr-2"></i>
            Estatísticas
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <p class="text-2xl font-bold text-blue-600">{{ $user->photos()->count() }}</p>
                <p class="text-sm text-gray-600">Fotos</p>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <p class="text-2xl font-bold text-green-600">{{ $user->interests()->count() }}</p>
                <p class="text-sm text-gray-600">Interesses</p>
            </div>
            <div class="text-center p-4 bg-purple-50 rounded-lg">
                <p class="text-2xl font-bold text-purple-600">{{ $user->matches()->count() }}</p>
                <p class="text-sm text-gray-600">Matches</p>
            </div>
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <p class="text-2xl font-bold text-yellow-600">{{ $user->reportedBy()->count() }}</p>
                <p class="text-sm text-gray-600">Denúncias Recebidas</p>
            </div>
        </div>
    </div>

    <!-- Actions -->
    @if($user->id !== auth()->id())
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-cog text-gray-500 mr-2"></i>
            Ações Administrativas
        </h3>
        <div class="flex flex-wrap gap-2">
            @if($user->is_active)
                <form method="POST" action="{{ route('admin.users.deactivate', $user) }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 transition duration-200"
                            onclick="return confirm('Tem certeza que deseja desativar este usuário?')">
                        <i class="fas fa-ban mr-2"></i>Desativar Usuário
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.users.activate', $user) }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition duration-200">
                        <i class="fas fa-check mr-2"></i>Ativar Usuário
                    </button>
                </form>
            @endif

            @if(!$user->is_admin)
                <form method="POST" action="{{ route('admin.users.make-admin', $user) }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition duration-200"
                            onclick="return confirm('Tem certeza que deseja tornar este usuário administrador?')">
                        <i class="fas fa-crown mr-2"></i>Tornar Administrador
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.users.remove-admin', $user) }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition duration-200"
                            onclick="return confirm('Tem certeza que deseja remover os privilégios de administrador deste usuário?')">
                        <i class="fas fa-user-slash mr-2"></i>Remover Admin
                    </button>
                </form>
            @endif

            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition duration-200"
                        onclick="return confirm('Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita!')">
                    <i class="fas fa-trash mr-2"></i>Excluir Usuário
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection

