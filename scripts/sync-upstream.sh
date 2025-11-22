#!/bin/bash

# Script para sincronizar mudanças do repositório upstream (projeto original)
# Uso: ./scripts/sync-upstream.sh

set -e

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

print_header() {
    echo -e "\n${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${BLUE}$1${NC}"
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}\n"
}

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    print_error "Este script deve ser executado no diretório raiz do projeto Laravel!"
    exit 1
fi

# Verificar se Git está inicializado
if [ ! -d ".git" ]; then
    print_error "Repositório Git não encontrado!"
    print_info "Inicializando repositório Git..."
    
    read -p "Deseja inicializar o repositório Git agora? (s/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[SsYy]$ ]]; then
        git init
        print_success "Repositório Git inicializado"
        print_warning "Você precisará fazer o primeiro commit antes de continuar"
        exit 0
    else
        print_error "Operação cancelada. Inicialize o Git manualmente primeiro."
        exit 1
    fi
fi

print_header "🔄 SINCRONIZANDO COM UPSTREAM"

# Verificar se upstream está configurado
print_info "Verificando remotes configurados..."
if ! git remote | grep -q "^upstream$"; then
    print_warning "Remote 'upstream' não encontrado!"
    echo
    print_info "Você precisa configurar o upstream com a URL do repositório original"
    read -p "Digite a URL do repositório original (upstream): " UPSTREAM_URL
    
    if [ -z "$UPSTREAM_URL" ]; then
        print_error "URL não fornecida. Operação cancelada."
        exit 1
    fi
    
    git remote add upstream "$UPSTREAM_URL"
    print_success "Upstream configurado: $UPSTREAM_URL"
else
    UPSTREAM_URL=$(git remote get-url upstream)
    print_success "Upstream já configurado: $UPSTREAM_URL"
fi

# Mostrar remotes configurados
echo
print_info "Remotes configurados:"
git remote -v

# Verificar branch atual
CURRENT_BRANCH=$(git branch --show-current)
print_info "Branch atual: $CURRENT_BRANCH"

# Verificar se há mudanças não commitadas
if ! git diff-index --quiet HEAD --; then
    print_warning "Há mudanças não commitadas no diretório de trabalho!"
    git status --short
    
    read -p "Deseja fazer stash das mudanças antes de sincronizar? (s/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[SsYy]$ ]]; then
        git stash push -m "Stash antes de sync upstream $(date +%Y%m%d_%H%M%S)"
        STASHED=true
        print_success "Mudanças guardadas em stash"
    else
        print_warning "Prosseguindo sem stash. Pode haver conflitos."
        STASHED=false
    fi
else
    STASHED=false
fi

# Buscar mudanças do upstream
print_header "⬇️  BUSCANDO MUDANÇAS DO UPSTREAM"
print_info "Executando: git fetch upstream"
if git fetch upstream; then
    print_success "Mudanças do upstream buscadas com sucesso"
else
    print_error "Falha ao buscar mudanças do upstream"
    if [ "$STASHED" = true ]; then
        git stash pop
    fi
    exit 1
fi

# Verificar qual é a branch principal do upstream
UPSTREAM_MAIN="main"
if ! git show-ref --verify --quiet refs/remotes/upstream/main; then
    if git show-ref --verify --quiet refs/remotes/upstream/master; then
        UPSTREAM_MAIN="master"
    else
        print_error "Não foi possível determinar a branch principal do upstream"
        print_info "Branches disponíveis no upstream:"
        git branch -r | grep upstream
        read -p "Digite o nome da branch principal do upstream: " UPSTREAM_MAIN
    fi
fi

print_info "Branch principal do upstream: $UPSTREAM_MAIN"

# Verificar se há commits novos
LOCAL_COMMIT=$(git rev-parse HEAD)
UPSTREAM_COMMIT=$(git rev-parse upstream/$UPSTREAM_MAIN)

if [ "$LOCAL_COMMIT" = "$UPSTREAM_COMMIT" ]; then
    print_success "Já está atualizado! Não há novas mudanças no upstream."
    if [ "$STASHED" = true ]; then
        git stash pop
    fi
    exit 0
fi

# Mostrar commits que serão incorporados
echo
print_info "Commits que serão incorporados:"
git log --oneline HEAD..upstream/$UPSTREAM_MAIN | head -10
if [ $(git rev-list --count HEAD..upstream/$UPSTREAM_MAIN) -gt 10 ]; then
    print_info "... e mais $(($(git rev-list --count HEAD..upstream/$UPSTREAM_MAIN) - 10)) commits"
fi

# Perguntar método de sincronização
echo
print_info "Escolha o método de sincronização:"
echo "1) Merge (recomendado - mais seguro)"
echo "2) Rebase (mantém histórico limpo, mas pode causar problemas se já fez push)"
read -p "Escolha (1 ou 2): " METHOD

if [ "$METHOD" = "2" ]; then
    print_warning "Usando REBASE. Certifique-se de que não há outros colaboradores trabalhando na mesma branch!"
    read -p "Tem certeza que deseja usar rebase? (s/n): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[SsYy]$ ]]; then
        METHOD="1"
    fi
fi

# Incorporar mudanças
print_header "🔀 INCORPORANDO MUDANÇAS"

if [ "$METHOD" = "2" ]; then
    print_info "Executando: git rebase upstream/$UPSTREAM_MAIN"
    if git rebase upstream/$UPSTREAM_MAIN; then
        print_success "Rebase concluído com sucesso!"
    else
        print_error "Conflitos durante o rebase!"
        print_info "Resolva os conflitos e execute: git rebase --continue"
        print_info "Ou cancele com: git rebase --abort"
        if [ "$STASHED" = true ]; then
            print_info "Para restaurar seu stash: git stash pop"
        fi
        exit 1
    fi
else
    print_info "Executando: git merge upstream/$UPSTREAM_MAIN"
    if git merge upstream/$UPSTREAM_MAIN --no-edit; then
        print_success "Merge concluído com sucesso!"
    else
        print_error "Conflitos durante o merge!"
        print_info "Resolva os conflitos, adicione os arquivos e faça commit:"
        print_info "  git add arquivo-resolvido.php"
        print_info "  git commit"
        print_info "Ou cancele com: git merge --abort"
        if [ "$STASHED" = true ]; then
            print_info "Para restaurar seu stash: git stash pop"
        fi
        exit 1
    fi
fi

# Restaurar stash se foi feito
if [ "$STASHED" = true ]; then
    echo
    print_info "Restaurando mudanças do stash..."
    if git stash pop; then
        print_success "Mudanças restauradas do stash"
    else
        print_warning "Houve conflitos ao restaurar o stash. Resolva manualmente:"
        print_info "  git stash list"
        print_info "  git stash show -p stash@{0}"
    fi
fi

# Verificar status final
echo
print_header "📊 STATUS FINAL"
git status

# Perguntar se deseja fazer push
echo
read -p "Deseja fazer push das mudanças para seu fork (origin)? (s/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[SsYy]$ ]]; then
    if [ "$METHOD" = "2" ]; then
        print_warning "Usando --force-with-lease porque foi usado rebase"
        if git push origin $CURRENT_BRANCH --force-with-lease; then
            print_success "Push concluído com sucesso!"
        else
            print_error "Falha ao fazer push"
            print_info "Se necessário, use: git push origin $CURRENT_BRANCH --force (cuidado!)"
        fi
    else
        if git push origin $CURRENT_BRANCH; then
            print_success "Push concluído com sucesso!"
        else
            print_error "Falha ao fazer push"
        fi
    fi
else
    print_info "Push não realizado. Execute manualmente quando estiver pronto:"
    if [ "$METHOD" = "2" ]; then
        print_info "  git push origin $CURRENT_BRANCH --force-with-lease"
    else
        print_info "  git push origin $CURRENT_BRANCH"
    fi
fi

echo
print_success "Sincronização concluída! 🎉"

