<?php
// Script para gerar todas as traduções de interesses

$categories = [
    'music' => [
        'Rock', 'Pop', 'Hip-Hop/Rap', 'Eletrônica', 'Jazz', 'Blues', 'Country',
        'R&B', 'Reggae', 'Samba', 'Bossa Nova', 'MPB', 'Funk', 'Pagode',
        'Sertanejo', 'Forró', 'Axé', 'Gospel', 'Clássica', 'Indie', 'Metal',
        'Punk', 'Folk', 'Soul', 'Disco', 'Trap', 'Drill', 'Lo-Fi'
    ],
    'sports' => [
        'Futebol', 'Basquete', 'Vôlei', 'Tênis', 'Natação', 'Corrida',
        'Ciclismo', 'Musculação', 'Yoga', 'Pilates', 'CrossFit', 'Boxe',
        'Jiu-Jitsu', 'Karatê', 'Taekwondo', 'Surf', 'Skate', 'Patinação',
        'Hockey', 'Rugby', 'Golfe', 'Tênis de Mesa', 'Badminton', 'Handebol',
        'Atletismo', 'Triatlo', 'Maratona', 'Caminhada', 'Dança', 'Balé',
        'Capoeira', 'Artes Marciais', 'Fitness', 'Ginástica', 'Pole Dance'
    ],
    'literature' => [
        'Ficção Científica', 'Fantasia', 'Romance', 'Mistério', 'Suspense',
        'Terror', 'Drama', 'Comédia', 'Biografia', 'Autobiografia', 'História',
        'Filosofia', 'Poesia', 'Crônicas', 'Contos', 'Literatura Brasileira',
        'Literatura Estrangeira', 'Clássicos', 'Contemporânea', 'Distopia',
        'Utopia', 'Realismo Mágico', 'Literatura Infantil', 'Graphic Novels',
        'Mangás', 'Quadrinhos', 'Livros Técnicos', 'Autoajuda', 'Religião',
        'Espiritualidade', 'Psicologia', 'Sociologia', 'Política'
    ],
    'cinema_tv' => [
        'Ação', 'Aventura', 'Comédia', 'Drama', 'Terror', 'Suspense',
        'Ficção Científica', 'Fantasia', 'Romance', 'Documentário', 'Biografia',
        'Histórico', 'Guerra', 'Western', 'Musical', 'Animação', 'Thriller',
        'Crime', 'Noir', 'Independente', 'Arte', 'Experimental', 'Séries',
        'Reality Shows', 'Documentários', 'Stand-up', 'Talk Shows', 'Novelas',
        'Sitcoms', 'Dramas Médicos', 'Policiais', 'Espionagem', 'Super-heróis'
    ],
    'hobbies' => [
        'Fotografia', 'Pintura', 'Desenho', 'Escultura', 'Artesanato',
        'Costura', 'Tricô', 'Crochê', 'Bordado', 'Culinária', 'Café',
        'Vinho', 'Cerveja', 'Chá', 'Jardinagem', 'Plantas', 'Aquarismo',
        'Colecionismo', 'Filatelia', 'Numismática', 'Quebra-cabeças',
        'Jogos de Tabuleiro', 'RPG', 'Video Games', 'Streaming', 'Podcasts',
        'Audiobooks', 'Meditação', 'Mindfulness', 'Astronomia', 'Observação de Aves',
        'Caminhadas na Natureza', 'Acampamento', 'Pesca', 'Caça', 'Arco e Flecha'
    ],
    'travel' => [
        'Viagem de Aventura', 'Viagem Cultural', 'Viagem de Luxo', 'Mochilão',
        'Viagem Romântica', 'Viagem em Família', 'Viagem Solo', 'Viagem em Grupo',
        'Ecoturismo', 'Turismo Rural', 'Turismo Urbano', 'Turismo Histórico',
        'Turismo Gastronômico', 'Turismo Religioso', 'Turismo de Negócios',
        'Cruzeiros', 'Road Trip', 'Trekking', 'Montanhismo', 'Praia',
        'Montanha', 'Deserto', 'Floresta', 'Cidade Grande', 'Cidade Pequena',
        'Europa', 'América do Norte', 'América do Sul', 'Ásia', 'África',
        'Oceania', 'Brasil', 'Interior', 'Capitais', 'Ilhas'
    ],
    'food' => [
        'Culinária Brasileira', 'Culinária Italiana', 'Culinária Japonesa',
        'Culinária Chinesa', 'Culinária Mexicana', 'Culinária Francesa',
        'Culinária Indiana', 'Culinária Tailandesa', 'Culinária Árabe',
        'Vegetariana', 'Vegana', 'Paleo', 'Keto', 'Mediterrânea', 'Fusion',
        'Fast Food', 'Gourmet', 'Street Food', 'Doces', 'Salgados',
        'Pratos Picantes', 'Pratos Suaves', 'Frutos do Mar', 'Carnes',
        'Massas', 'Pizzas', 'Hambúrgueres', 'Sobremesas', 'Sorvetes',
        'Chocolate', 'Café', 'Chá', 'Sucos', 'Smoothies', 'Cocktails'
    ],
    'technology' => [
        'Programação', 'Desenvolvimento Web', 'Mobile', 'Inteligência Artificial',
        'Machine Learning', 'Data Science', 'Blockchain', 'Criptomoedas',
        'Realidade Virtual', 'Realidade Aumentada', 'IoT', 'Robótica',
        'Gadgets', 'Smartphones', 'Tablets', 'Laptops', 'Gaming',
        'Streaming', 'Podcasts', 'YouTube', 'TikTok', 'Instagram',
        'Redes Sociais', 'E-commerce', 'Startups', 'Inovação', 'Futuro',
        'Sustentabilidade', 'Energia Renovável', 'Carros Elétricos'
    ]
];

$translations = [
    'en' => [],
    'es' => []
];

// Função para criar a chave de tradução
function createKey($text) {
    return strtolower(str_replace([' ', '/', '-', '&', 'ç'], ['_', '_', '_', 'e', 'c'], $text));
}

// Função para traduzir (versão simplificada - mantém a maioria igual)
function translateEN($text) {
    $translations = [
        'Ação' => 'Action',
        'Aventura' => 'Adventure',
        'Culinária Brasileira' => 'Brazilian Cuisine',
        'Culinária Italiana' => 'Italian Cuisine',
        'Culinária Japonesa' => 'Japanese Cuisine',
        'Culinária Chinesa' => 'Chinese Cuisine',
        'Culinária Mexicana' => 'Mexican Cuisine',
        'Culinária Francesa' => 'French Cuisine',
        'Culinária Indiana' => 'Indian Cuisine',
        'Culinária Tailandesa' => 'Thai Cuisine',
        'Culinária Árabe' => 'Arab Cuisine',
    ];
    return $translations[$text] ?? $text;
}

function translateES($text) {
    $translations = [
        'Ação' => 'Acción',
        'Aventura' => 'Aventura',
    ];
    return $translations[$text] ?? $text;
}

// Gerar chaves para PT (já temos a maioria no seeder)
echo "// Portuguese (PT)\n";
foreach ($categories as $category => $options) {
    foreach ($options as $option) {
        $key = createKey($option);
        echo "    'interests.option.$key' => '$option',\n";
    }
}

echo "\n// English (EN)\n";
foreach ($categories as $category => $options) {
    foreach ($options as $option) {
        $key = createKey($option);
        $translated = translateEN($option);
        echo "    'interests.option.$key' => '$translated',\n";
    }
}

echo "\n// Spanish (ES)\n";
foreach ($categories as $category => $options) {
    foreach ($options as $option) {
        $key = createKey($option);
        $translated = translateES($option);
        echo "    'interests.option.$key' => '$translated',\n";
    }
}
