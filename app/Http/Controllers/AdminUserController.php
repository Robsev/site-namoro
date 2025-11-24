<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes group (auth, admin)
    }

    /**
     * Display a listing of users with filters.
     */
    public function index(Request $request)
    {
        $query = User::with(['profile', 'photos', 'subscriptions']);

        // Filters
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($isAdmin = $request->get('is_admin')) {
            $query->where('is_admin', $isAdmin === 'yes');
        }

        // Subscription type filter removed - all users have premium features now
        // if ($subscriptionType = $request->get('subscription_type')) {
        //     // All users are treated as premium
        // }

        if ($verified = $request->get('verified')) {
            if ($verified === 'yes') {
                $query->whereNotNull('email_verified_at');
            } elseif ($verified === 'no') {
                $query->whereNull('email_verified_at');
            }
        }

        if ($gender = $request->get('gender')) {
            $query->where('gender', $gender);
        }

        // Order by
        $orderBy = $request->get('order_by', 'created_at');
        $orderDir = $request->get('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);

        $users = $query->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'admins' => User::where('is_admin', true)->count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'premium' => User::count(), // All users have premium features now
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Show a specific user.
     */
    public function show(User $user)
    {
        $user->load([
            'profile',
            'photos',
            'interests',
            'matchingPreferences',
            'subscriptions',
            'psychologicalProfile',
            'reports',
            'reportedBy'
        ]);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Activate a user.
     */
    public function activate(User $user)
    {
        $user->update(['is_active' => true]);

        return redirect()->route('admin.users.index')
            ->with('success', "Usuário {$user->name} ativado com sucesso!");
    }

    /**
     * Deactivate a user.
     */
    public function deactivate(User $user)
    {
        $user->update(['is_active' => false]);

        return redirect()->route('admin.users.index')
            ->with('success', "Usuário {$user->name} desativado com sucesso!");
    }

    /**
     * Make a user admin.
     */
    public function makeAdmin(User $user)
    {
        $user->update(['is_admin' => true]);

        return redirect()->route('admin.users.index')
            ->with('success', "Usuário {$user->name} agora é administrador!");
    }

    /**
     * Remove admin privileges from a user.
     */
    public function removeAdmin(User $user)
    {
        // Prevent removing admin from yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Você não pode remover seus próprios privilégios de administrador!');
        }

        $user->update(['is_admin' => false]);

        return redirect()->route('admin.users.index')
            ->with('success', "Privilégios de administrador removidos de {$user->name}!");
    }

    /**
     * Update user information.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'is_active' => 'sometimes|boolean',
            'is_admin' => 'sometimes|boolean',
        ]);

        $user->update($request->only(['name', 'email', 'is_active', 'is_admin']));

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Informações do usuário atualizadas com sucesso!');
    }

    /**
     * Delete a user (soft delete if implemented, or hard delete).
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Você não pode excluir sua própria conta!');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Usuário {$userName} excluído com sucesso!");
    }
}

