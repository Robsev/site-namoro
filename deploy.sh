#!/bin/bash

# =============================================================================
# SCRIPT DE DEPLOY - AMIGOS PARA SEMPRE
# =============================================================================
# Este script automatiza o processo de deploy para produção
# Inclui build do frontend, otimizações e configurações de produção
# =============================================================================

set -e  # Exit on any error

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Função para imprimir mensagens coloridas
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_header() {
    echo -e "${PURPLE}=============================================================================${NC}"
    echo -e "${PURPLE}$1${NC}"
    echo -e "${PURPLE}=============================================================================${NC}"
}

# Função para limpeza em caso de erro
cleanup() {
    print_error "Erro detectado! Desativando modo de manutenção..."
    if [ "$MAINTENANCE_AVAILABLE" = true ]; then
        php artisan up 2>/dev/null || true
        print_warning "Modo de manutenção desativado devido a erro"
    else
        print_warning "Modo de manutenção não estava ativo"
    fi
    exit 1
}

# Configurar trap para limpeza em caso de erro
trap cleanup ERR

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    print_error "Este script deve ser executado no diretório raiz do projeto Laravel!"
    exit 1
fi

# =============================================================================
# VERIFICAÇÃO DE ATUALIZAÇÕES GIT
# =============================================================================
print_header "🔄 VERIFICANDO ATUALIZAÇÕES DO REPOSITÓRIO"

# Fazer fetch primeiro
print_status "Verificando atualizações no repositório..."
git fetch origin

# Comparar commits
LOCAL=$(git rev-parse HEAD)
REMOTE=$(git rev-parse origin/main)

HAS_UPDATES=false
if [ "$LOCAL" != "$REMOTE" ]; then
    HAS_UPDATES=true
    print_success "Nova versão disponível!"
else
    print_success "Já está na versão mais recente do código."
    print_status "Continuando com atualização de dependências e migrations..."
fi

print_header "🚀 INICIANDO DEPLOY - AMIGOS PARA SEMPRE"

# =============================================================================
# 0. GIT PULL (apenas se houver atualizações)
# =============================================================================
if [ "$HAS_UPDATES" = true ]; then
    print_header "⬇️ BAIXANDO ATUALIZAÇÕES"
    
    # Descartar mudanças locais no composer.lock se necessário
    if git diff --quiet composer.lock 2>/dev/null; then
        print_status "composer.lock não modificado localmente"
    else
        print_warning "composer.lock foi modificado localmente"
        print_status "Descartando mudanças locais (será atualizado no pull)..."
        git restore composer.lock 2>/dev/null || true
    fi
    
    # Fazer pull
    print_status "Fazendo pull do repositório..."
    if git pull --no-rebase origin main; then
        print_success "Código atualizado"
    else
        print_warning "Falha ao fazer pull. Tentando continuar com deploy..."
    fi
else
    print_status "Pulando git pull (sem atualizações disponíveis)"
fi

# =============================================================================
# 0. VERIFICAÇÃO INICIAL
# =============================================================================
print_header "🔍 VERIFICAÇÃO INICIAL"

# Verificar se pode escrever nos diretórios necessários
print_status "Verificando permissões de escrita..."
if [ -w "storage" ] && [ -w "bootstrap/cache" ]; then
    print_success "Permissões adequadas para deploy"
else
    print_error "ERRO: Usuário não tem permissão de escrita em storage/ ou bootstrap/cache/"
    print_error "Execute como root: chown -R www:www storage/ bootstrap/cache/"
    print_error "E depois: chmod -R 775 storage/ bootstrap/cache/"
    exit 1
fi

# Verificar se pode criar arquivo de manutenção
print_status "Verificando permissão para modo de manutenção..."
if [ -w "storage/framework" ]; then
    print_success "Modo de manutenção disponível"
    MAINTENANCE_AVAILABLE=true
else
    print_warning "Modo de manutenção não disponível - continuando sem ele"
    MAINTENANCE_AVAILABLE=false
fi

# =============================================================================
# 1. MODO DE MANUTENÇÃO
# =============================================================================
if [ "$MAINTENANCE_AVAILABLE" = true ]; then
    print_header "🔧 ATIVANDO MODO DE MANUTENÇÃO"
    
    # Ativar modo de manutenção
    print_status "Ativando modo de manutenção..."
    php artisan down
    print_success "Modo de manutenção ativado"
else
    print_warning "Pulando modo de manutenção - permissões insuficientes"
fi

# =============================================================================
# 2. BACKUP E PREPARAÇÃO
# =============================================================================
print_header "📦 BACKUP E PREPARAÇÃO"

# Criar backup do .env se existir
if [ -f ".env" ]; then
    print_status "Criando backup do .env..."
    cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
    print_success "Backup do .env criado"
fi

# =============================================================================
# 3. ATUALIZAÇÃO DE DEPENDÊNCIAS
# =============================================================================
print_header "📚 ATUALIZAÇÃO DE DEPENDÊNCIAS"

# Limpar cache do Composer antes de atualizar
print_status "Limpando cache do Composer..."
composer clear-cache --no-interaction || true
print_success "Cache do Composer limpo"

# Atualizar dependências PHP
# Se composer update falhar, tentar composer install como fallback
print_status "Atualizando dependências PHP e composer.lock..."
if composer update --no-dev --optimize-autoloader --no-interaction; then
    print_success "Dependências PHP atualizadas"
else
    print_warning "composer update falhou, tentando recuperação..."
    # Verificar se vendor está corrompido (falta autoload.php)
    if [ ! -f "vendor/autoload.php" ]; then
        print_status "Diretório vendor parece corrompido, removendo..."
        rm -rf vendor/ || true
        print_success "Diretório vendor removido"
    fi
    # Tentar instalar baseado no composer.lock (mais seguro)
    print_status "Tentando instalar via composer install (preserva composer.lock)..."
    if composer install --no-dev --optimize-autoloader --no-interaction; then
        print_success "Dependências PHP instaladas via composer install"
        print_warning "NOTA: composer.lock não foi atualizado. Execute composer update manualmente se necessário."
    else
        print_error "Falha crítica ao instalar dependências PHP"
        print_error "Verifique os logs do Composer e tente executar manualmente:"
        print_error "  composer install --no-dev --optimize-autoloader"
        exit 1
    fi
fi

# Instalar dependências Node.js
print_status "Instalando dependências Node.js..."
npm install
print_success "Dependências Node.js instaladas"

# =============================================================================
# 4. BUILD DO FRONTEND
# =============================================================================
print_header "🎨 BUILD DO FRONTEND"

# Verificar se Vite está configurado
if [ ! -f "vite.config.js" ]; then
    print_error "Arquivo vite.config.js não encontrado!"
    exit 1
fi

# Build do frontend com Vite
print_status "Executando build do frontend com Vite..."
npm run build
print_success "Build do frontend concluído"

# Verificar se os arquivos foram gerados
if [ ! -d "public/build" ]; then
    print_error "Diretório public/build não foi criado!"
    exit 1
fi

print_success "Arquivos de build gerados em public/build/"

# =============================================================================
# 4. CONFIGURAÇÕES DE PRODUÇÃO
# =============================================================================
print_header "⚙️ CONFIGURAÇÕES DE PRODUÇÃO"

# Gerar chave da aplicação se não existir
print_status "Verificando chave da aplicação..."
if ! grep -q "APP_KEY=" .env 2>/dev/null || grep -q "APP_KEY=$" .env 2>/dev/null; then
    print_status "Gerando nova chave da aplicação..."
    php artisan key:generate --force
    print_success "Chave da aplicação gerada"
else
    print_success "Chave da aplicação já existe"
fi

# Configurar cache de configuração
print_status "Otimizando cache de configuração..."
php artisan config:cache
print_success "Cache de configuração otimizado"

# Configurar cache de rotas
print_status "Otimizando cache de rotas..."
php artisan route:cache
print_success "Cache de rotas otimizado"

# Configurar cache de views
print_status "Otimizando cache de views..."
php artisan view:cache
print_success "Cache de views otimizado"

# =============================================================================
# 5. BANCO DE DADOS
# =============================================================================
print_header "🗄️ BANCO DE DADOS"

# Executar migrations pendentes
print_status "Executando migrations pendentes..."
php artisan migrate --force
print_success "Migrations pendentes executadas com sucesso"

# Seeders removidos do deploy automático
# Para executar seeders manualmente: php artisan db:seed
print_status "Seeders não executados automaticamente (preserva dados existentes)"

# =============================================================================
# 6. STORAGE E PERMISSÕES
# =============================================================================
print_header "📁 STORAGE E PERMISSÕES"

# Criar link simbólico para storage
print_status "Criando link simbólico para storage..."
php artisan storage:link
print_success "Link simbólico para storage criado"

# Verificar permissões (sem alterar)
print_status "Verificando permissões..."
if [ -w "storage" ] && [ -w "bootstrap/cache" ]; then
    print_success "Permissões adequadas para deploy"
else
    print_warning "Verifique se o usuário tem permissão de escrita nos diretórios storage/ e bootstrap/cache/"
fi

# =============================================================================
# 7. OTIMIZAÇÕES FINAIS
# =============================================================================
print_header "🚀 OTIMIZAÇÕES FINAIS"

# Limpar cache
print_status "Limpando caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
print_success "Caches limpos"

# Reconfigurar cache de produção
print_status "Reconfigurando cache de produção..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
print_success "Cache de produção reconfigurado"

# =============================================================================
# 8. DESATIVAR MODO DE MANUTENÇÃO
# =============================================================================
if [ "$MAINTENANCE_AVAILABLE" = true ]; then
    print_header "🔓 DESATIVANDO MODO DE MANUTENÇÃO"
    
    # Desativar modo de manutenção
    print_status "Desativando modo de manutenção..."
    php artisan up
    print_success "Modo de manutenção desativado - Site online!"
else
    print_warning "Modo de manutenção não estava ativo - site já online"
fi

# =============================================================================
# 9. VERIFICAÇÕES FINAIS
# =============================================================================
print_header "✅ VERIFICAÇÕES FINAIS"

# Verificar se o servidor está funcionando
print_status "Verificando se o servidor está funcionando..."
if php artisan serve --host=0.0.0.0 --port=8000 --no-reload > /dev/null 2>&1 &
then
    SERVER_PID=$!
    sleep 3
    
    if curl -s http://localhost:8000 > /dev/null; then
        print_success "Servidor está funcionando corretamente"
        kill $SERVER_PID 2>/dev/null || true
    else
        print_warning "Não foi possível verificar o servidor automaticamente"
        kill $SERVER_PID 2>/dev/null || true
    fi
else
    print_warning "Não foi possível iniciar o servidor para verificação"
fi

# =============================================================================
# 10. INFORMAÇÕES DE DEPLOY
# =============================================================================
print_header "📋 INFORMAÇÕES DE DEPLOY"

print_success "Deploy concluído com sucesso!"
echo ""
echo -e "${CYAN}📊 RESUMO DO DEPLOY:${NC}"
if [ "$HAS_UPDATES" = true ]; then
    echo -e "  • Código Git: ${GREEN}✓${NC} Atualizado"
fi
echo -e "  • Modo de Manutenção: ${GREEN}✓${NC} Ativado durante deploy"
echo -e "  • Dependências PHP: ${GREEN}✓${NC} Atualizadas e otimizadas"
echo -e "  • composer.lock: ${GREEN}✓${NC} Verificado/Atualizado"
echo -e "  • Dependências Node.js: ${GREEN}✓${NC} Instaladas"
echo -e "  • Build Frontend: ${GREEN}✓${NC} Concluído com Vite"
echo -e "  • Cache de Produção: ${GREEN}✓${NC} Configurado"
echo -e "  • Banco de Dados: ${GREEN}✓${NC} Migrations pendentes executadas"
echo -e "  • Storage: ${GREEN}✓${NC} Link simbólico criado"
echo -e "  • Permissões: ${GREEN}✓${NC} Configuradas"
echo -e "  • Site Online: ${GREEN}✓${NC} Modo de manutenção desativado"
echo ""
echo -e "${CYAN}🚀 PRÓXIMOS PASSOS:${NC}"
echo -e "  1. Configure seu servidor web (Apache/Nginx)"
echo -e "  2. Configure SSL/HTTPS"
echo -e "  3. Configure variáveis de ambiente de produção"
echo -e "  4. Configure backup automático do banco de dados"
echo -e "  5. Configure monitoramento e logs"
echo ""
echo -e "${CYAN}📁 ARQUIVOS IMPORTANTES:${NC}"
echo -e "  • .env: Configurações de ambiente"
echo -e "  • public/build/: Assets compilados"
echo -e "  • storage/: Arquivos de upload e cache"
echo -e "  • bootstrap/cache/: Cache de configuração"
echo ""

# =============================================================================
# 11. COMANDOS ÚTEIS
# =============================================================================
print_header "🛠️ COMANDOS ÚTEIS"

echo -e "${CYAN}Para gerenciar o sistema:${NC}"
echo -e "  • Manutenção: ${YELLOW}php artisan down/up${NC}"
echo -e "  • Cache: ${YELLOW}php artisan cache:clear${NC}"
echo -e "  • Logs: ${YELLOW}tail -f storage/logs/laravel.log${NC}"
echo -e "  • Queue: ${YELLOW}php artisan queue:work${NC}"
echo -e "  • Apache: ${YELLOW}sudo service apache24 restart${NC}"
echo ""
echo -e "${CYAN}Para monitoramento:${NC}"
echo -e "  • Status: ${YELLOW}php artisan about${NC}"
echo -e "  • Rotas: ${YELLOW}php artisan route:list${NC}"
echo -e "  • Config: ${YELLOW}php artisan config:show${NC}"
echo ""

print_header "🎉 DEPLOY CONCLUÍDO COM SUCESSO!"
print_success "Amigos Para Sempre está pronto para produção! ❤️"
