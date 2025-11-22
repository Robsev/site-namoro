<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpdateLogoAndFavicon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logo:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza logomarca e favicon do site';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎨 Atualizando logomarca e favicon...');

        // Verificar se as imagens existem
        $logoPath = public_path('images/logo/logo.png');
        $faviconPath = public_path('images/icons/favicon.ico');

        if (!File::exists($logoPath)) {
            $this->warn('⚠️  Logomarca não encontrada em: ' . $logoPath);
            $this->info('📁 Coloque sua logomarca em: public/images/logo/logo.png');
            $this->info('💡 Depois execute: git add . && git commit -m "feat: Adicionar logomarca"');
        } else {
            $this->info('✅ Logomarca encontrada!');
        }

        if (!File::exists($faviconPath)) {
            $this->warn('⚠️  Favicon não encontrado em: ' . $faviconPath);
            $this->info('📁 Coloque seu favicon em: public/images/icons/favicon.ico');
            $this->info('💡 Depois execute: git add . && git commit -m "feat: Adicionar favicon"');
        } else {
            $this->info('✅ Favicon encontrado!');
        }

        // Atualizar layout principal
        $this->updateLayout();

        $this->info('🎉 Atualização concluída!');
        $this->info('🌐 Acesse o site para ver as mudanças.');
        $this->info('🚀 Para produção: git push origin main');
    }

    private function updateLayout()
    {
        $layoutPath = resource_path('views/layouts/profile.blade.php');
        
        if (!File::exists($layoutPath)) {
            $this->error('❌ Layout não encontrado!');
            return;
        }

        $layoutContent = File::get($layoutPath);

        // Adicionar favicon se não existir
        if (strpos($layoutContent, 'favicon') === false) {
            $faviconCode = '    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset(\'images/icons/favicon.ico\') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset(\'images/icons/favicon-32x32.png\') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset(\'images/icons/favicon-16x16.png\') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset(\'images/icons/apple-touch-icon.png\') }}">
    <link rel="manifest" href="{{ asset(\'images/icons/site.webmanifest\') }}">';

            // Inserir após a tag <title>
            $layoutContent = str_replace(
                '<title>@yield(\'title\', \'Perfil\') - {{ config(\'app.name\', \'Sintonia de Amor\') }}</title>',
                '<title>@yield(\'title\', \'Perfil\') - {{ config(\'app.name\', \'Sintonia de Amor\') }}</title>' . "\n" . $faviconCode,
                $layoutContent
            );

            File::put($layoutPath, $layoutContent);
            $this->info('✅ Favicon adicionado ao layout!');
        }

        // Atualizar logo na navegação
        $logoCode = '@if(File::exists(public_path(\'images/logo/logo.png\')))
                        <img src="{{ asset(\'images/logo/logo.png\') }}" alt="{{ config(\'app.name\') }}" class="h-8 w-auto">
                    @else
                        <i class="fas fa-heart mr-2"></i>
                        <span class="hidden sm:inline">Sintonia de Amor</span>
                        <span class="sm:hidden">APS</span>
                    @endif';

        // Substituir o logo atual
        $layoutContent = str_replace(
            '<i class="fas fa-heart mr-2"></i>
                            <span class="hidden sm:inline">Sintonia de Amor</span>
                            <span class="sm:hidden">APS</span>',
            $logoCode,
            $layoutContent
        );

        File::put($layoutPath, $layoutContent);
        $this->info('✅ Logo atualizado na navegação!');
    }
}
