<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InterestCategory;

class InterestCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Música',
                'slug' => 'music',
                'description' => 'Gêneros musicais e preferências musicais',
                'options' => [
                    'Rock', 'Pop', 'Hip-Hop/Rap', 'Eletrônica', 'Jazz', 'Blues', 'Country',
                    'R&B', 'Reggae', 'Samba', 'Bossa Nova', 'MPB', 'Funk', 'Pagode',
                    'Sertanejo', 'Forró', 'Axé', 'Gospel', 'Clássica', 'Indie', 'Metal',
                    'Punk', 'Folk', 'Soul', 'Disco', 'Trap', 'Drill', 'Lo-Fi'
                ],
                'max_selections' => 5,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Esportes',
                'slug' => 'sports',
                'description' => 'Esportes e atividades físicas',
                'options' => [
                    'Futebol', 'Basquete', 'Vôlei', 'Tênis', 'Natação', 'Corrida',
                    'Ciclismo', 'Musculação', 'Yoga', 'Pilates', 'CrossFit', 'Boxe',
                    'Jiu-Jitsu', 'Karatê', 'Taekwondo', 'Surf', 'Skate', 'Patinação',
                    'Hockey', 'Rugby', 'Golfe', 'Tênis de Mesa', 'Badminton', 'Handebol',
                    'Atletismo', 'Triatlo', 'Maratona', 'Caminhada', 'Dança', 'Balé',
                    'Capoeira', 'Artes Marciais', 'Fitness', 'Ginástica', 'Pole Dance'
                ],
                'max_selections' => 8,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Literatura',
                'slug' => 'literature',
                'description' => 'Gêneros literários e tipos de leitura',
                'options' => [
                    'Ficção Científica', 'Fantasia', 'Romance', 'Mistério', 'Suspense',
                    'Terror', 'Drama', 'Comédia', 'Biografia', 'Autobiografia', 'História',
                    'Filosofia', 'Poesia', 'Crônicas', 'Contos', 'Literatura Brasileira',
                    'Literatura Estrangeira', 'Clássicos', 'Contemporânea', 'Distopia',
                    'Utopia', 'Realismo Mágico', 'Literatura Infantil', 'Graphic Novels',
                    'Mangás', 'Quadrinhos', 'Livros Técnicos', 'Autoajuda', 'Religião',
                    'Espiritualidade', 'Psicologia', 'Sociologia', 'Política'
                ],
                'max_selections' => 5,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Cinema e TV',
                'slug' => 'cinema_tv',
                'description' => 'Gêneros cinematográficos e televisivos',
                'options' => [
                    'Ação', 'Aventura', 'Comédia', 'Drama', 'Terror', 'Suspense',
                    'Ficção Científica', 'Fantasia', 'Romance', 'Documentário', 'Biografia',
                    'Histórico', 'Guerra', 'Western', 'Musical', 'Animação', 'Thriller',
                    'Crime', 'Noir', 'Independente', 'Arte', 'Experimental', 'Séries',
                    'Reality Shows', 'Documentários', 'Stand-up', 'Talk Shows', 'Novelas',
                    'Sitcoms', 'Dramas Médicos', 'Policiais', 'Espionagem', 'Super-heróis'
                ],
                'max_selections' => 5,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Hobbies',
                'slug' => 'hobbies',
                'description' => 'Passatempos e atividades de lazer',
                'options' => [
                    'Fotografia', 'Pintura', 'Desenho', 'Escultura', 'Artesanato',
                    'Costura', 'Tricô', 'Crochê', 'Bordado', 'Culinária', 'Café',
                    'Vinho', 'Cerveja', 'Chá', 'Jardinagem', 'Plantas', 'Aquarismo',
                    'Colecionismo', 'Filatelia', 'Numismática', 'Quebra-cabeças',
                    'Jogos de Tabuleiro', 'RPG', 'Video Games', 'Streaming', 'Podcasts',
                    'Audiobooks', 'Meditação', 'Mindfulness', 'Astronomia', 'Observação de Aves',
                    'Caminhadas na Natureza', 'Acampamento', 'Pesca', 'Caça', 'Arco e Flecha'
                ],
                'max_selections' => 6,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Viagens',
                'slug' => 'travel',
                'description' => 'Tipos de viagem e destinos preferidos',
                'options' => [
                    'Viagem de Aventura', 'Viagem Cultural', 'Viagem de Luxo', 'Mochilão',
                    'Viagem Romântica', 'Viagem em Família', 'Viagem Solo', 'Viagem em Grupo',
                    'Ecoturismo', 'Turismo Rural', 'Turismo Urbano', 'Turismo Histórico',
                    'Turismo Gastronômico', 'Turismo Religioso', 'Turismo de Negócios',
                    'Cruzeiros', 'Road Trip', 'Trekking', 'Montanhismo', 'Praia',
                    'Montanha', 'Deserto', 'Floresta', 'Cidade Grande', 'Cidade Pequena',
                    'Europa', 'América do Norte', 'América do Sul', 'Ásia', 'África',
                    'Oceania', 'Brasil', 'Interior', 'Capitais', 'Ilhas'
                ],
                'max_selections' => 4,
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Alimentação',
                'slug' => 'food',
                'description' => 'Preferências alimentares e culinárias',
                'options' => [
                    'Culinária Brasileira', 'Culinária Italiana', 'Culinária Japonesa',
                    'Culinária Chinesa', 'Culinária Mexicana', 'Culinária Francesa',
                    'Culinária Indiana', 'Culinária Tailandesa', 'Culinária Árabe',
                    'Vegetariana', 'Vegana', 'Paleo', 'Keto', 'Mediterrânea', 'Fusion',
                    'Fast Food', 'Gourmet', 'Street Food', 'Doces', 'Salgados',
                    'Pratos Picantes', 'Pratos Suaves', 'Frutos do Mar', 'Carnes',
                    'Massas', 'Pizzas', 'Hambúrgueres', 'Sobremesas', 'Sorvetes',
                    'Chocolate', 'Café', 'Chá', 'Sucos', 'Smoothies', 'Cocktails'
                ],
                'max_selections' => 4,
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Tecnologia',
                'slug' => 'technology',
                'description' => 'Interesses tecnológicos e digitais',
                'options' => [
                    'Programação', 'Desenvolvimento Web', 'Mobile', 'Inteligência Artificial',
                    'Machine Learning', 'Data Science', 'Blockchain', 'Criptomoedas',
                    'Realidade Virtual', 'Realidade Aumentada', 'IoT', 'Robótica',
                    'Gadgets', 'Smartphones', 'Tablets', 'Laptops', 'Gaming',
                    'Streaming', 'Podcasts', 'YouTube', 'TikTok', 'Instagram',
                    'Redes Sociais', 'E-commerce', 'Startups', 'Inovação', 'Futuro',
                    'Sustentabilidade', 'Energia Renovável', 'Carros Elétricos'
                ],
                'max_selections' => 6,
                'is_active' => true,
                'sort_order' => 8,
            ],
        ];

        foreach ($categories as $category) {
            InterestCategory::create($category);
        }
    }
}
