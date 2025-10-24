#!/bin/bash

# Script para configurar permissões iniciais do projeto
# Uso: ./scripts/setup-permissions.sh

echo "🔧 Configurando permissões iniciais do projeto..."

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    echo "❌ Este script deve ser executado no diretório raiz do projeto Laravel!"
    exit 1
fi

# 1. Configurar proprietário dos arquivos
echo "📁 Configurando proprietário dos arquivos..."
sudo chown -R www-data:www-data storage/
sudo chown -R www-data:www-data bootstrap/cache/
sudo chown -R www-data:www-data public/storage/ 2>/dev/null || true

# 2. Configurar permissões básicas
echo "🔐 Configurando permissões básicas..."
sudo chmod -R 775 storage/
sudo chmod -R 775 bootstrap/cache/
sudo chmod -R 775 public/storage/ 2>/dev/null || true

# 3. Adicionar usuário ao grupo www-data
echo "👤 Adicionando usuário ao grupo www-data..."
sudo usermod -a -G www-data $USER

# 4. Configurar ACL (Access Control Lists) se disponível
echo "🔑 Configurando ACL para permissões avançadas..."
if command -v setfacl &> /dev/null; then
    # Configurar ACL para o usuário atual
    sudo setfacl -R -m u:$USER:rwx storage/ 2>/dev/null || true
    sudo setfacl -R -m u:$USER:rwx bootstrap/cache/ 2>/dev/null || true
    sudo setfacl -R -m u:$USER:rwx public/storage/ 2>/dev/null || true
    
    # Configurar ACL padrão para novos arquivos
    sudo setfacl -R -d -m u:$USER:rwx storage/ 2>/dev/null || true
    sudo setfacl -R -d -m u:$USER:rwx bootstrap/cache/ 2>/dev/null || true
    sudo setfacl -R -d -m u:$USER:rwx public/storage/ 2>/dev/null || true
    
    echo "✅ ACL configurado com sucesso"
else
    echo "⚠️ ACL não disponível, usando permissões tradicionais"
fi

# 5. Configurar permissões específicas do Laravel
echo "🎯 Configurando permissões específicas do Laravel..."
sudo chmod -R 755 public/
sudo chmod -R 644 public/*.php 2>/dev/null || true
sudo chmod -R 644 public/*.html 2>/dev/null || true

# 6. Configurar permissões de logs
echo "📝 Configurando permissões de logs..."
sudo chmod -R 775 storage/logs/
sudo chown -R www-data:www-data storage/logs/

# 7. Verificar configuração
echo "🔍 Verificando configuração..."
echo "Proprietário do storage:"
ls -la storage/ | head -5
echo ""
echo "Proprietário do bootstrap/cache:"
ls -la bootstrap/cache/ | head -5
echo ""
echo "Grupos do usuário atual:"
groups $USER

echo ""
echo "✅ Configuração de permissões concluída!"
echo "💡 Reinicie sua sessão para que as mudanças de grupo tenham efeito"
echo "💡 Execute: newgrp www-data ou faça logout/login"
