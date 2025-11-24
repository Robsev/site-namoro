#!/bin/bash

# Script para compilar assets do Vite em produção
# Uso: ./build-assets.sh

set -e

echo "🔨 Compilando assets do Vite..."

# Verificar se Node.js está instalado
if ! command -v node &> /dev/null; then
    echo "❌ Node.js não está instalado. Por favor, instale Node.js primeiro."
    exit 1
fi

# Verificar se npm está instalado
if ! command -v npm &> /dev/null; then
    echo "❌ npm não está instalado. Por favor, instale npm primeiro."
    exit 1
fi

# Verificar se package.json existe
if [ ! -f "package.json" ]; then
    echo "❌ package.json não encontrado!"
    exit 1
fi

# Verificar se vite.config.js existe
if [ ! -f "vite.config.js" ]; then
    echo "❌ vite.config.js não encontrado!"
    exit 1
fi

# Instalar dependências se node_modules não existir
if [ ! -d "node_modules" ]; then
    echo "📦 Instalando dependências Node.js..."
    npm install
fi

# Compilar assets
echo "🏗️  Executando build do frontend..."
npm run build

# Verificar se o build foi bem-sucedido
if [ -d "public/build" ] && [ -f "public/build/manifest.json" ]; then
    echo "✅ Assets compilados com sucesso!"
    echo "📁 Diretório: public/build"
    echo "📄 Manifest: public/build/manifest.json"
else
    echo "❌ Erro: Build não gerou os arquivos esperados!"
    echo "Verifique os logs acima para mais detalhes."
    exit 1
fi





