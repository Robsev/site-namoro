<?php

namespace App\Http\Controllers;

use App\Models\InterestCategory;
use App\Models\UserInterest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InterestController extends Controller
{
    public function __construct()
    {
        // Middleware é aplicado nas rotas
    }

    /**
     * Mostrar página de interesses do usuário
     */
    public function index()
    {
        $user = Auth::user();
        $categories = InterestCategory::active()->ordered()->get();
        
        // Carregar interesses existentes do usuário
        $userInterests = $user->interests()->with('interestCategory')->get()
            ->groupBy('interest_category_id')
            ->map(function($interests) {
                return $interests->pluck('interest_value')->toArray();
            });

        return view('interests.index', compact('categories', 'userInterests'));
    }

    /**
     * Atualizar interesses do usuário
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'interests' => 'required|array',
            'interests.*' => 'array',
            'interests.*.*' => 'string|max:255',
        ]);

        // Deletar interesses existentes
        $user->interests()->delete();

        // Adicionar novos interesses
        foreach ($request->interests as $categoryId => $interests) {
            if (empty($interests)) continue;
            
            $category = InterestCategory::find($categoryId);
            if (!$category) continue;

            // Validar limite de seleções
            if (count($interests) > $category->max_selections) {
                return redirect()->back()->withErrors([
                    $category->slug => "Máximo de {$category->max_selections} seleções permitidas para {$category->name}"
                ]);
            }

            // Validar se os interesses estão na lista de opções
            foreach ($interests as $interest) {
                if (!in_array($interest, $category->options)) {
                    return redirect()->back()->withErrors([
                        $category->slug => "Interesse '{$interest}' não é válido para {$category->name}"
                    ]);
                }

                $user->interests()->create([
                    'interest_category_id' => $categoryId,
                    'interest_value' => $interest,
                    'preference_level' => 1, // Padrão
                    'is_public' => true,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Interesses atualizados com sucesso!');
    }

    /**
     * API: Obter categorias de interesses
     */
    public function getCategories()
    {
        $categories = InterestCategory::active()->ordered()->get();
        return response()->json($categories);
    }

    /**
     * API: Obter interesses do usuário
     */
    public function getUserInterests()
    {
        $user = Auth::user();
        $interests = $user->interests()->with('interestCategory')->get()
            ->groupBy('interest_category_id')
            ->map(function($interests) {
                return $interests->pluck('interest_value')->toArray();
            });

        return response()->json($interests);
    }

    /**
     * Calcular compatibilidade de interesses entre usuários
     */
    public function calculateCompatibility($userId1, $userId2)
    {
        $user1 = User::find($userId1);
        $user2 = User::find($userId2);
        
        if (!$user1 || !$user2) {
            return 0;
        }

        $interests1 = $user1->interests()->pluck('interest_value')->toArray();
        $interests2 = $user2->interests()->pluck('interest_value')->toArray();

        $commonInterests = array_intersect($interests1, $interests2);
        $totalInterests = count(array_unique(array_merge($interests1, $interests2)));

        if ($totalInterests === 0) {
            return 0;
        }

        $compatibility = (count($commonInterests) / $totalInterests) * 100;
        return round($compatibility, 2);
    }
}
