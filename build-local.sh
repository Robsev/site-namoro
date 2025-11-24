#!/bin/bash

# =============================================================================
# SCRIPT DE BUILD LOCAL - SINTONIA DE AMOR
# =============================================================================
# Este script executa o build do frontend localmente e prepara os arquivos
# para commit no repositório. Use este script sempre que modificar arquivos
# do frontend (CSS, JS, etc).
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

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    print_error "Este script deve ser executado no diretório raiz do projeto Laravel!"
    exit 1
fi

print_header "🎨 BUILD LOCAL DO FRONTEND"

# Verificar se Node.js/npm está instalado
if ! command -v npm &> /dev/null; then
    print_error "npm não está instalado!"
    print_error "Instale Node.js e npm para continuar."
    print_error "Visite: https://nodejs.org/"
    exit 1
fi

print_success "Node.js/npm encontrado: $(node --version) / $(npm --version)"

# Instalar dependências Node.js (se necessário)
print_status "Verificando dependências Node.js..."
if [ ! -d "node_modules" ] || [ "package.json" -nt "node_modules" ]; then
    print_status "Instalando/atualizando dependências Node.js..."
    npm install
    print_success "Dependências Node.js instaladas"
else
    print_success "Dependências Node.js já estão atualizadas"
fi

# Verificar se Vite está configurado
if [ ! -f "vite.config.js" ]; then
    print_error "Arquivo vite.config.js não encontrado!"
    exit 1
fi

# Executar build do frontend
print_status "Executando build do frontend com Vite..."
npm run build

# Verificar se os arquivos foram gerados
if [ ! -d "public/build" ]; then
    print_error "Diretório public/build não foi criado!"
    exit 1
fi

if [ ! -f "public/build/manifest.json" ]; then
    print_error "Arquivo public/build/manifest.json não foi criado!"
    exit 1
fi

print_success "Build do frontend concluído com sucesso!"
print_success "Arquivos gerados em public/build/"

# Mostrar informações sobre os arquivos gerados
echo ""
print_status "📊 Arquivos gerados:"
ls -lh public/build/ | tail -n +2 | awk '{print "  • " $9 " (" $5 ")"}'

# Verificar se os arquivos estão sendo rastreados pelo git
print_status "Verificando status do git..."
if git ls-files --error-unmatch public/build/ > /dev/null 2>&1; then
    print_success "Arquivos de build estão sendo rastreados pelo git"
else
    print_warning "Arquivos de build ainda não estão sendo rastreados pelo git"
    print_status "Adicionando arquivos ao git..."
    git add public/build/
    print_success "Arquivos adicionados ao git"
fi

# Mostrar status do git
echo ""
print_header "📋 STATUS DO GIT"
git status --short public/build/ || true

echo ""
print_success "✅ Build local concluído!"
echo ""
echo -e "${CYAN}📝 PRÓXIMOS PASSOS:${NC}"
echo -e "  1. Revise as mudanças: ${YELLOW}git status${NC}"
echo -e "  2. Faça commit dos arquivos de build:"
echo -e "     ${YELLOW}git add public/build/${NC}"
echo -e "     ${YELLOW}git commit -m \"build: atualizar assets do frontend\"${NC}"
echo -e "  3. Faça push para o repositório:"
echo -e "     ${YELLOW}git push origin main${NC}"
echo -e "  4. Em produção, execute: ${YELLOW}./deploy.sh${NC}"
echo ""
print_success "🎉 Pronto para commit!"

