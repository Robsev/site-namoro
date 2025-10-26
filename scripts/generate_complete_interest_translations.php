<?php

$categories = [
    'music' => ['Music', 'Música'],
    'sports' => ['Sports', 'Deportes'],
    'literature' => ['Literature', 'Literatura'],
    'cinema_tv' => ['Cinema & TV', 'Cine y TV'],
    'hobbies' => ['Hobbies', 'Pasatiempos'],
    'travel' => ['Travel', 'Viajes'],
    'food' => ['Food', 'Alimentación'],
    'technology' => ['Technology', 'Tecnología'],
];

$descriptions = [
    'music' => ['Musical genres and preferences', 'Géneros musicales y preferencias musicales'],
    'sports' => ['Sports and physical activities', 'Deportes y actividades físicas'],
    'literature' => ['Literary genres and reading types', 'Géneros literarios y tipos de lectura'],
    'cinema_tv' => ['Film and TV genres', 'Géneros cinematográficos y televisivos'],
    'hobbies' => ['Pastimes and leisure activities', 'Pasatiempos y actividades de ocio'],
    'travel' => ['Types of travel and preferred destinations', 'Tipos de viaje y destinos preferidos'],
    'food' => ['Food preferences and cuisines', 'Preferencias alimentarias y culinarias'],
    'technology' => ['Technology and digital interests', 'Intereses tecnológicos y digitales'],
];

// All options from the seeder
$allOptions = [
    'Rock', 'Pop', 'Hip-Hop/Rap', 'Eletrônica', 'Jazz', 'Blues', 'Country',
    'R&B', 'Reggae', 'Samba', 'Bossa Nova', 'MPB', 'Funk', 'Pagode',
    'Sertanejo', 'Forró', 'Axé', 'Gospel', 'Clássica', 'Indie', 'Metal',
    'Punk', 'Folk', 'Soul', 'Disco', 'Trap', 'Drill', 'Lo-Fi',
    'Futebol', 'Basquete', 'Vôlei', 'Tênis', 'Natação', 'Corrida',
    'Ciclismo', 'Musculação', 'Yoga', 'Pilates', 'CrossFit', 'Boxe',
    'Jiu-Jitsu', 'Karatê', 'Taekwondo', 'Surf', 'Skate', 'Patinação',
    'Hockey', 'Rugby', 'Golfe', 'Tênis de Mesa', 'Badminton', 'Handebol',
    'Atletismo', 'Triatlo', 'Maratona', 'Caminhada', 'Dança', 'Balé',
    'Capoeira', 'Artes Marciais', 'Fitness', 'Ginástica', 'Pole Dance',
    'Ficção Científica', 'Fantasia', 'Romance', 'Mistério', 'Suspense',
    'Terror', 'Drama', 'Comédia', 'Biografia', 'Autobiografia', 'História',
    'Filosofia', 'Poesia', 'Crônicas', 'Contos', 'Literatura Brasileira',
    'Literatura Estrangeira', 'Clássicos', 'Contemporânea', 'Distopia',
    'Utopia', 'Realismo Mágico', 'Literatura Infantil', 'Graphic Novels',
    'Mangás', 'Quadrinhos', 'Livros Técnicos', 'Autoajuda', 'Religião',
    'Espiritualidade', 'Psicologia', 'Sociologia', 'Política',
    'Ação', 'Aventura', 'Documentário', 'Histórico', 'Guerra',
    'Western', 'Musical', 'Animação', 'Thriller', 'Crime',
    'Noir', 'Independente', 'Arte', 'Experimental', 'Séries',
    'Reality Shows', 'Stand-up', 'Talk Shows', 'Novelas', 'Sitcoms',
    'Dramas Médicos', 'Policiais', 'Espionagem', 'Super-heróis',
    'Fotografia', 'Pintura', 'Desenho', 'Escultura', 'Artesanato',
    'Costura', 'Tricô', 'Crochê', 'Bordado', 'Culinária', 'Café',
    'Vinho', 'Cerveja', 'Chá', 'Jardinagem', 'Plantas', 'Aquarismo',
    'Colecionismo', 'Filatelia', 'Numismática', 'Quebra-cabeças',
    'Jogos de Tabuleiro', 'RPG', 'Video Games', 'Streaming', 'Podcasts',
    'Audiobooks', 'Meditação', 'Mindfulness', 'Astronomia', 'Observação de Aves',
    'Caminhadas na Natureza', 'Acampamento', 'Pesca', 'Caça', 'Arco e Flecha',
    'Viagem de Aventura', 'Viagem Cultural', 'Viagem de Luxo', 'Mochilão',
    'Viagem Romântica', 'Viagem em Família', 'Viagem Solo', 'Viagem em Grupo',
    'Ecoturismo', 'Turismo Rural', 'Turismo Urbano', 'Turismo Histórico',
    'Turismo Gastronômico', 'Turismo Religioso', 'Turismo de Negócios',
    'Cruzeiros', 'Road Trip', 'Trekking', 'Montanhismo', 'Praia',
    'Montanha', 'Deserto', 'Floresta', 'Cidade Grande', 'Cidade Pequena',
    'Europa', 'América do Norte', 'América do Sul', 'Ásia', 'África',
    'Oceania', 'Brasil', 'Interior', 'Capitais', 'Ilhas',
    'Culinária Brasileira', 'Culinária Italiana', 'Culinária Japonesa',
    'Culinária Chinesa', 'Culinária Mexicana', 'Culinária Francesa',
    'Culinária Indiana', 'Culinária Tailandesa', 'Culinária Árabe',
    'Vegetariana', 'Vegana', 'Paleo', 'Keto', 'Mediterrânea', 'Fusion',
    'Fast Food', 'Gourmet', 'Street Food', 'Doces', 'Salgados',
    'Pratos Picantes', 'Pratos Suaves', 'Frutos do Mar', 'Carnes',
    'Massas', 'Pizzas', 'Hambúrgueres', 'Sobremesas', 'Sorvetes',
    'Chocolate', 'Sucos', 'Smoothies', 'Cocktails',
    'Programação', 'Desenvolvimento Web', 'Mobile', 'Inteligência Artificial',
    'Machine Learning', 'Data Science', 'Blockchain', 'Criptomoedas',
    'Realidade Virtual', 'Realidade Aumentada', 'IoT', 'Robótica',
    'Gadgets', 'Smartphones', 'Tablets', 'Laptops', 'Gaming',
    'YouTube', 'TikTok', 'Instagram',
    'Redes Sociais', 'E-commerce', 'Startups', 'Inovação', 'Futuro',
    'Sustentabilidade', 'Energia Renovável', 'Carros Elétricos'
];

// Translate option to EN/ES (many are identical)
function translateTo($option, $lang) {
    $translations = [
        'Eletrônica' => ['Electronic', 'Electrónica'],
        'Clássica' => ['Classical', 'Clásica'],
        'Futebol' => ['Soccer', 'Fútbol'],
        'Basquete' => ['Basketball', 'Baloncesto'],
        'Vôlei' => ['Volleyball', 'Voleibol'],
        'Tênis' => ['Tennis', 'Tenis'],
        'Natação' => ['Swimming', 'Natación'],
        'Corrida' => ['Running', 'Correr'],
        'Ciclismo' => ['Cycling', 'Ciclismo'],
        'Musculação' => ['Bodybuilding', 'Musculación'],
        'Boxe' => ['Boxing', 'Boxeo'],
        'Karatê' => ['Karate', 'Kárate'],
        'Surf' => ['Surfing', 'Surf'],
        'Skate' => ['Skateboarding', 'Skate'],
        'Patinação' => ['Skating', 'Patinaje'],
        'Golfe' => ['Golf', 'Golf'],
        'Tênis de Mesa' => ['Table Tennis', 'Tenis de Mesa'],
        'Badminton' => ['Badminton', 'Bádminton'],
        'Handebol' => ['Handball', 'Balonmano'],
        'Atletismo' => ['Athletics', 'Atletismo'],
        'Triatlo' => ['Triathlon', 'Triatlón'],
        'Maratona' => ['Marathon', 'Maratón'],
        'Caminhada' => ['Walking', 'Caminata'],
        'Dança' => ['Dance', 'Baile'],
        'Balé' => ['Ballet', 'Ballet'],
        'Ginástica' => ['Gymnastics', 'Gimnasia'],
        'Ação' => ['Action', 'Acción'],
        'Documentário' => ['Documentary', 'Documental'],
        'Histórico' => ['Historical', 'Histórico'],
        'Animação' => ['Animation', 'Animación'],
        'Thriller' => ['Thriller', 'Thriller'],
        'Noir' => ['Noir', 'Noir'],
        'Experimental' => ['Experimental', 'Experimental'],
        'Séries' => ['Series', 'Series'],
        'Reality Shows' => ['Reality Shows', 'Reality Shows'],
        'Stand-up' => ['Stand-up', 'Stand-up'],
        'Talk Shows' => ['Talk Shows', 'Talk Shows'],
        'Sitcoms' => ['Sitcoms', 'Sitcoms'],
        'Dramas Médicos' => ['Medical Dramas', 'Dramas Médicos'],
        'Policiais' => ['Police', 'Policíacas'],
        'Espionagem' => ['Spy', 'Espionaje'],
        'Super-heróis' => ['Superheroes', 'Superhéroes'],
        // Add more as needed
    ];

    if (isset($translations[$option])) {
        return $translations[$option][$lang === 'en' ? 0 : 1];
    }

    // Default: return as-is (many terms are the same in all languages)
    return $option;
}

// Generate key from option
function optionToKey($option) {
    return strtolower(str_replace([' ', '/', '-', '&'], '_', $option));
}

echo "Generating translations for EN and ES...\n\n";

// Generate EN file content
echo "=== EN ===\n\n";
foreach ($categories as $key => $value) {
    echo "    'interests.category.$key' => '{$value[0]}',\n";
}

foreach ($descriptions as $key => $value) {
    echo "    'interests.description.$key' => '{$value[0]}',\n";
}

foreach ($allOptions as $option) {
    $key = optionToKey($option);
    $en = translateTo($option, 'en');
    echo "    'interests.option.$key' => '$en',\n";
}

echo "\n\n=== ES ===\n\n";
foreach ($categories as $key => $value) {
    echo "    'interests.category.$key' => '{$value[1]}',\n";
}

foreach ($descriptions as $key => $value) {
    echo "    'interests.description.$key' => '{$value[1]}',\n";
}

foreach ($allOptions as $option) {
    $key = optionToKey($option);
    $es = translateTo($option, 'es');
    echo "    'interests.option.$key' => '$es',\n";
}

echo "\nDone!\n";

