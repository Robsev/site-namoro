#!/bin/bash

# =============================================================================
# SCRIPT DE INSTALAÇÃO DOS GIT HOOKS
# =============================================================================
# Este script instala/configura os git hooks para build automático
# Execute este script após clonar o repositório ou após npm install
# =============================================================================

set -e

# Cores
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_status "🔧 Configurando git hooks para build automático..."

# Verificar se estamos no diretório correto
if [ ! -f "package.json" ]; then
    echo "❌ Este script deve ser executado no diretório raiz do projeto!"
    exit 1
fi

# Verificar se npm está instalado
if ! command -v npm &> /dev/null; then
    print_warning "npm não está instalado. Instale Node.js primeiro."
    exit 1
fi

# Instalar Husky se ainda não estiver instalado
if [ ! -d "node_modules/husky" ]; then
    print_status "Instalando Husky..."
    npm install
fi

# Executar script de setup do Husky
print_status "Configurando hooks..."
node scripts/setup-husky.js

# Tornar hooks executáveis (Unix/Linux/Mac)
if [ -f ".husky/pre-commit" ]; then
    chmod +x .husky/pre-commit 2>/dev/null || true
    chmod +x .husky/_/husky.sh 2>/dev/null || true
fi

print_success "✅ Git hooks configurados com sucesso!"
echo ""
echo "📝 Agora, sempre que você modificar arquivos do frontend e fizer commit,"
echo "   o build será executado automaticamente!"
echo ""
echo "💡 Arquivos monitorados:"
echo "   • resources/css/"
echo "   • resources/js/"
echo "   • vite.config.js"
echo "   • package.json"
echo "   • tailwind.config.js"
echo "   • postcss.config.js"

