#!/bin/bash

# Script para resolver problemas de rebase em produção
# Uso: ./scripts/fix-production-rebase.sh

echo "🔧 Resolvendo problema de rebase em produção..."

# 1. Verificar status atual
echo "📊 Verificando status do Git..."
git status

# 2. Fazer stash das mudanças nos .gitignore
echo "💾 Fazendo stash das mudanças nos .gitignore..."
git stash push -m "Temporary stash of .gitignore changes"

# 3. Fazer pull sem rebase
echo "⬇️ Fazendo pull sem rebase..."
git pull --no-rebase

# 4. Aplicar stash de volta
echo "🔄 Aplicando stash de volta..."
git stash pop

# 5. Verificar se há conflitos
if [ $? -eq 0 ]; then
    echo "✅ Stash aplicado com sucesso!"
else
    echo "⚠️ Conflitos detectados no stash. Resolvendo..."
    # Se houver conflitos, vamos resolver manualmente
    echo "🔍 Verificando arquivos com conflitos..."
    git status
fi

# 6. Verificar status final
echo "📊 Status final:"
git status

echo "🎉 Script executado com sucesso!"
echo "💡 Se ainda houver problemas, execute: git reset --hard HEAD"
