@extends('layouts.admin')

@section('title', 'Gestão de Usuários - Admin')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-users text-blue-500 mr-2"></i>
                Gestão de Usuários
            </h2>
            <p class="mt-2 text-gray-600">Gerencie e monitore todos os usuários do sistema</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg">
            <p class="text-sm font-medium text-gray-500">Total</p>
            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-green-50 p-4 rounded-lg">
            <p class="text-sm font-medium text-gray-500">Ativos</p>
            <p class="text-2xl font-semibold text-gray-900">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-red-50 p-4 rounded-lg">
            <p class="text-sm font-medium text-gray-500">Inativos</p>
            <p class="text-2xl font-semibold text-gray-900">{{ $stats['inactive'] }}</p>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg">
            <p class="text-sm font-medium text-gray-500">Administradores</p>
            <p class="text-2xl font-semibold text-gray-900">{{ $stats['admins'] }}</p>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg">
            <p class="text-sm font-medium text-gray-500">Verificados</p>
            <p class="text-2xl font-semibold text-gray-900">{{ $stats['verified'] }}</p>
        </div>
        <div class="bg-indigo-50 p-4 rounded-lg">
            <p class="text-sm font-medium text-gray-500">Premium</p>
            <p class="text-2xl font-semibold text-gray-900">{{ $stats['premium'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="bg-gray-50 rounded-lg p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Nome, e-mail..." 
                       class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativos</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativos</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Assinatura</label>
                <select name="subscription_type" class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos</option>
                    <option value="premium" {{ request('subscription_type') === 'premium' ? 'selected' : '' }}>Premium</option>
                    <option value="free" {{ request('subscription_type') === 'free' ? 'selected' : '' }}>Gratuito</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Administrador</label>
                <select name="is_admin" class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos</option>
                    <option value="yes" {{ request('is_admin') === 'yes' ? 'selected' : '' }}>Sim</option>
                    <option value="no" {{ request('is_admin') === 'no' ? 'selected' : '' }}>Não</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Verificado</label>
                <select name="verified" class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos</option>
                    <option value="yes" {{ request('verified') === 'yes' ? 'selected' : '' }}>Sim</option>
                    <option value="no" {{ request('verified') === 'no' ? 'selected' : '' }}>Não</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gênero</label>
                <select name="gender" class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos</option>
                    <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Masculino</option>
                    <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Feminino</option>
                    <option value="other" {{ request('gender') === 'other' ? 'selected' : '' }}>Outro</option>
                    <option value="prefer_not_to_say" {{ request('gender') === 'prefer_not_to_say' ? 'selected' : '' }}>Prefere não dizer</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ordenar por</label>
                <select name="order_by" class="w-full rounded border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    <option value="created_at" {{ request('order_by', 'created_at') === 'created_at' ? 'selected' : '' }}>Data de Cadastro</option>
                    <option value="name" {{ request('order_by') === 'name' ? 'selected' : '' }}>Nome</option>
                    <option value="email" {{ request('order_by') === 'email' ? 'selected' : '' }}>E-mail</option>
                    <option value="gender" {{ request('order_by') === 'gender' ? 'selected' : '' }}>Gênero</option>
                    <option value="last_seen" {{ request('order_by') === 'last_seen' ? 'selected' : '' }}>Última Visita</option>
                </select>
            </div>
        </div>
        <div class="mt-4 flex gap-2">
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-900 transition duration-200">
                <i class="fas fa-filter mr-2"></i>Filtrar
            </button>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition duration-200">
                <i class="fas fa-times mr-2"></i>Limpar
            </a>
        </div>
    </form>

    <!-- Users Table -->
    @if($users->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuário</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">E-mail</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gênero</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assinatura</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cadastro</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-900">#{{ $user->id }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    @if($user->profile_photo)
                                        <img src="{{ $user->profile_photo_url }}" 
                                             alt="{{ $user->name }}" 
                                             class="h-10 w-10 rounded-full object-cover mr-3">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-gray-600"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $user->name }}
                                            @if($user->is_admin)
                                                <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                                    <i class="fas fa-crown"></i> Admin
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            @if($user->last_seen)
                                                Última visita: {{ $user->last_seen->diffForHumans() }}
                                            @else
                                                Nunca acessou
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $user->email }}
                                @if($user->email_verified_at)
                                    <span class="ml-2 text-green-600" title="E-mail verificado">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                @else
                                    <span class="ml-2 text-yellow-600" title="E-mail não verificado">
                                        <i class="fas fa-exclamation-circle"></i>
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                @if($user->gender)
                                    @php
                                        $genderLabels = [
                                            'male' => 'Masculino',
                                            'female' => 'Feminino',
                                            'other' => 'Outro',
                                            'prefer_not_to_say' => 'Prefere não dizer'
                                        ];
                                        $genderLabel = $genderLabels[$user->gender] ?? ucfirst($user->gender);
                                    @endphp
                                    {{ $genderLabel }}
                                @else
                                    <span class="text-gray-400">Não informado</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($user->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Ativo
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i> Inativo
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                @if($user->hasActivePremiumSubscription())
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                        <i class="fas fa-star mr-1"></i> Premium
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Gratuito
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $user->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.users.show', $user) }}" 
                                       class="text-blue-600 hover:text-blue-900" 
                                       title="Ver detalhes">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($user->is_active)
                                        <form method="POST" action="{{ route('admin.users.deactivate', $user) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-yellow-600 hover:text-yellow-900" 
                                                    title="Desativar usuário"
                                                    onclick="return confirm('Tem certeza que deseja desativar este usuário?')">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.activate', $user) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-green-600 hover:text-green-900" 
                                                    title="Ativar usuário">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if(!$user->is_admin)
                                        <form method="POST" action="{{ route('admin.users.make-admin', $user) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-purple-600 hover:text-purple-900" 
                                                    title="Tornar administrador"
                                                    onclick="return confirm('Tem certeza que deseja tornar este usuário administrador?')">
                                                <i class="fas fa-crown"></i>
                                            </button>
                                        </form>
                                    @elseif($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.remove-admin', $user) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900" 
                                                    title="Remover privilégios de admin"
                                                    onclick="return confirm('Tem certeza que deseja remover os privilégios de administrador deste usuário?')">
                                                <i class="fas fa-user-slash"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900" 
                                                    title="Excluir usuário"
                                                    onclick="return confirm('Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita!')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-users text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum usuário encontrado</h3>
            <p class="text-gray-500">Tente ajustar os filtros de busca.</p>
        </div>
    @endif
</div>
@endsection

