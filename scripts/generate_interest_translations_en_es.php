<?php

$categories = [
    'music' => ['Music', 'Géneros musicales y preferencias musicales'],
    'sports' => ['Sports', 'Deportes y actividades físicas'],
    'literature' => ['Literature', 'Géneros literarios y tipos de lectura'],
    'cinema_tv' => ['Cinema & TV', 'Géneros cinematográficos y televisivos'],
    'hobbies' => ['Hobbies', 'Pasatiempos y actividades de ocio'],
    'travel' => ['Travel', 'Tipos de viaje y destinos preferidos'],
    'food' => ['Food', 'Preferencias alimentarias y culinarias'],
    'technology' => ['Technology', 'Intereses tecnológicos y digitales'],
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

$translations = [
    // Music
    'rock' => ['Rock', 'Rock'],
    'pop' => ['Pop', 'Pop'],
    'hip_hop_rap' => ['Hip-Hop/Rap', 'Hip-Hop/Rap'],
    'eletrônica' => ['Electronic', 'Electrónica'],
    'jazz' => ['Jazz', 'Jazz'],
    'blues' => ['Blues', 'Blues'],
    'country' => ['Country', 'Country'],
    'rb' => ['R&B', 'R&B'],
    'reggae' => ['Reggae', 'Reggae'],
    'samba' => ['Samba', 'Samba'],
    'bossa_nova' => ['Bossa Nova', 'Bossa Nova'],
    'mpb' => ['MPB', 'MPB'],
    'funk' => ['Funk', 'Funk'],
    'pagode' => ['Pagode', 'Pagode'],
    'sertanejo' => ['Sertanejo', 'Sertanejo'],
    'forró' => ['Forró', 'Forró'],
    'axé' => ['Axé', 'Axé'],
    'gospel' => ['Gospel', 'Gospel'],
    'clássica' => ['Classical', 'Clásica'],
    'indie' => ['Indie', 'Indie'],
    'metal' => ['Metal', 'Metal'],
    'punk' => ['Punk', 'Punk'],
    'folk' => ['Folk', 'Folk'],
    'soul' => ['Soul', 'Soul'],
    'disco' => ['Disco', 'Disco'],
    'trap' => ['Trap', 'Trap'],
    'drill' => ['Drill', 'Drill'],
    'lo_fi' => ['Lo-Fi', 'Lo-Fi'],

    // Sports
    'futebol' => ['Soccer', 'Fútbol'],
    'basquete' => ['Basketball', 'Baloncesto'],
    'vôlei' => ['Volleyball', 'Voleibol'],
    'tênis' => ['Tennis', 'Tenis'],
    'natação' => ['Swimming', 'Natación'],
    'corrida' => ['Running', 'Running'],
    'ciclismo' => ['Cycling', 'Ciclismo'],
    'musculação' => ['Bodybuilding', 'Musculación'],
    'yoga' => ['Yoga', 'Yoga'],
    'pilates' => ['Pilates', 'Pilates'],
    'crossfit' => ['CrossFit', 'CrossFit'],
    'boxe' => ['Boxing', 'Boxeo'],
    'jiu_jitsu' => ['Jiu-Jitsu', 'Jiu-Jitsu'],
    'karatê' => ['Karate', 'Kárate'],
    'taekwondo' => ['Taekwondo', 'Taekwondo'],
    'surf' => ['Surfing', 'Surf'],
    'skate' => ['Skateboarding', 'Skate'],
    'patinação' => ['Skating', 'Patinaje'],
    'hockey' => ['Hockey', 'Hockey'],
    'rugby' => ['Rugby', 'Rugby'],
    'golfe' => ['Golf', 'Golf'],
    'tênis_de_mesa' => ['Table Tennis', 'Tenis de Mesa'],
    'badminton' => ['Badminton', 'Bádminton'],
    'handebol' => ['Handball', 'Balonmano'],
    'atletismo' => ['Athletics', 'Atletismo'],
    'triatlo' => ['Triathlon', 'Triatlón'],
    'maratona' => ['Marathon', 'Maratón'],
    'caminhada' => ['Walking', 'Caminata'],
    'dança' => ['Dance', 'Baile'],
    'balé' => ['Ballet', 'Ballet'],
    'capoeira' => ['Capoeira', 'Capoeira'],
    'artes_marciais' => ['Martial Arts', 'Artes Marciales'],
    'fitness' => ['Fitness', 'Fitness'],
    'ginástica' => ['Gymnastics', 'Gimnasia'],
    'pole_dance' => ['Pole Dance', 'Pole Dance'],

    // Literature - continued in next section due to length
];

echo "Generating translations...\n";

// Generate categories for EN
echo "\n// EN Categories:\n";
foreach ($categories as $key => $value) {
    echo "'interests.category.$key' => '{$value[0]}',\n";
}

// Generate descriptions for EN
echo "\n// EN Descriptions:\n";
foreach ($descriptions as $key => $value) {
    echo "'interests.description.$key' => '{$value[0]}',\n";
}

// Generate options for EN - using simplified mapping
echo "\n// EN Options:\n";
foreach ($translations as $key => $value) {
    echo "'interests.option.$key' => '{$value[0]}',\n";
}

// Generate for ES
echo "\n\n// ES Categories:\n";
foreach ($categories as $key => $value) {
    echo "'interests.category.$key' => '{$value[1]}',\n";
}

// Generate descriptions for ES
echo "\n// ES Descriptions:\n";
foreach ($descriptions as $key => $value) {
    echo "'interests.description.$key' => '{$value[1]}',\n";
}

// Generate options for ES
echo "\n// ES Options:\n";
foreach ($translations as $key => $value) {
    echo "'interests.option.$key' => '{$value[1]}',\n";
}

echo "\nDone!\n";

