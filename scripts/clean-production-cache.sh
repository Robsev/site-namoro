#!/bin/bash

# Script para limpar cache e resolver problemas de git em produção
# Uso: ./scripts/clean-production-cache.sh

echo "🧹 Limpando cache e resolvendo problemas de Git em produção..."

# 1. Parar serviços se necessário
echo "⏹️ Parando serviços..."
# systemctl stop nginx  # Descomente se necessário
# systemctl stop php8.2-fpm  # Descomente se necessário

# 2. Limpar cache do Laravel
echo "🗑️ Limpando cache do Laravel..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 3. Remover arquivos problemáticos do Git
echo "🔧 Removendo arquivos problemáticos do Git..."
git rm --cached bootstrap/cache/.gitignore 2>/dev/null || true
git rm --cached storage/app/.gitignore 2>/dev/null || true
git rm --cached storage/app/private/.gitignore 2>/dev/null || true
git rm --cached storage/app/public/.gitignore 2>/dev/null || true
git rm --cached storage/framework/.gitignore 2>/dev/null || true
git rm --cached storage/framework/cache/.gitignore 2>/dev/null || true
git rm --cached storage/framework/cache/data/.gitignore 2>/dev/null || true
git rm --cached storage/framework/sessions/.gitignore 2>/dev/null || true
git rm --cached storage/framework/testing/.gitignore 2>/dev/null || true
git rm --cached storage/framework/views/.gitignore 2>/dev/null || true
git rm --cached storage/logs/.gitignore 2>/dev/null || true

# 4. Fazer commit das mudanças
echo "💾 Fazendo commit das mudanças..."
git add .gitignore
git commit -m "fix: Ajustar .gitignore para evitar conflitos de cache" || echo "Nenhuma mudança para commitar"

# 5. Fazer pull
echo "⬇️ Fazendo pull..."
git pull --no-rebase

# 6. Reconstruir cache
echo "🔨 Reconstruindo cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Ajustar permissões
echo "🔐 Ajustando permissões..."
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# 8. Reiniciar serviços
echo "🔄 Reiniciando serviços..."
# systemctl start php8.2-fpm  # Descomente se necessário
# systemctl start nginx  # Descomente se necessário

echo "✅ Limpeza concluída com sucesso!"
echo "📊 Status do Git:"
git status
