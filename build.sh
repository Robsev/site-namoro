#!/bin/bash

# =============================================================================
# SCRIPT DE BUILD RÁPIDO - SINTONIA DE AMOR
# =============================================================================
# Script para build rápido do frontend em produção
# =============================================================================

set -e

# Cores
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

print_status() {
    echo -e "${BLUE}[BUILD]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_status "🚀 Iniciando build do frontend..."

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    echo "❌ Este script deve ser executado no diretório raiz do projeto Laravel!"
    exit 1
fi

# Instalar dependências Node.js
print_status "📦 Instalando dependências Node.js..."
npm install

# Build do frontend
print_status "🎨 Executando build do frontend..."
npm run build

# Verificar se o build foi bem-sucedido
if [ -d "public/build" ]; then
    print_success "✅ Build concluído com sucesso!"
    print_success "📁 Arquivos gerados em: public/build/"
    
    # Mostrar tamanho dos arquivos
    echo ""
    echo "📊 Tamanho dos arquivos:"
    du -h public/build/*
    
else
    echo "❌ Erro: Diretório public/build não foi criado!"
    exit 1
fi

print_success "🎉 Build do frontend concluído!"
