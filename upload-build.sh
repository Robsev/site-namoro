#!/bin/bash

# Script para fazer upload do diretório public/build para o servidor
# Uso: ./upload-build.sh [usuario@servidor:/caminho/remoto]

set -e

# Cores
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

print_status() {
    echo -e "${BLUE}[UPLOAD]${NC} $1"
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

# Verificar se public/build existe
if [ ! -d "public/build" ]; then
    print_error "Diretório public/build não encontrado!"
    print_status "Execute 'npm run build' primeiro para compilar os assets."
    exit 1
fi

# Verificar se manifest.json existe
if [ ! -f "public/build/manifest.json" ]; then
    print_error "manifest.json não encontrado em public/build!"
    print_status "Execute 'npm run build' primeiro para compilar os assets."
    exit 1
fi

# Verificar se SCP está disponível
if ! command -v scp &> /dev/null; then
    print_error "SCP não está instalado ou não está no PATH."
    print_status "Instale OpenSSH ou use um cliente SFTP/FTP alternativo."
    exit 1
fi

# Obter destino do servidor
if [ -z "$1" ]; then
    print_status "Uso: ./upload-build.sh usuario@servidor:/caminho/remoto"
    echo ""
    print_status "Exemplo para seu servidor:"
    echo "  ./upload-build.sh u399618994@br-asc-web1661:/home/u399618994/domains/sintoniadeamor.com.br/site-namoro/public/"
    echo ""
    read -p "Digite o destino do servidor: " DEST
else
    DEST="$1"
fi

# Validar formato do destino
if [[ ! "$DEST" =~ @.*: ]]; then
    print_error "Formato inválido. Use: usuario@servidor:/caminho/remoto"
    exit 1
fi

# Extrair caminho remoto
REMOTE_PATH="${DEST#*:}"

print_status "Preparando upload..."
print_status "Origem: $(pwd)/public/build"
print_status "Destino: $DEST"

# Confirmar
read -p "Continuar com o upload? (s/N): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Ss]$ ]]; then
    print_warning "Upload cancelado."
    exit 0
fi

# Fazer upload
print_status "Fazendo upload do diretório public/build..."
if scp -r public/build "$DEST"; then
    print_success "Upload concluído com sucesso!"
    print_status "Verifique no servidor: ls -la ${REMOTE_PATH}build/"
else
    print_error "Falha no upload!"
    print_status "Verifique:"
    print_status "  - Credenciais de acesso"
    print_status "  - Permissões do diretório remoto"
    print_status "  - Conexão com o servidor"
    exit 1
fi

