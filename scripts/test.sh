#!/bin/bash

# Script de Testes para Sintonia de Amor
# Executa todos os testes e gera relatórios

echo "🧪 Executando Testes do Sintonia de Amor..."
echo "=============================================="

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    echo "❌ Erro: Execute este script no diretório raiz do projeto Laravel"
    exit 1
fi

# Criar diretório para relatórios se não existir
mkdir -p tests/reports

echo "📋 Executando Testes Unitários..."
php artisan test --testsuite=Unit --coverage-html=tests/reports/coverage-unit

echo "📋 Executando Testes de Funcionalidade..."
php artisan test --testsuite=Feature --coverage-html=tests/reports/coverage-feature

echo "📋 Executando Todos os Testes..."
php artisan test --coverage-html=tests/reports/coverage-all

echo "📊 Gerando Relatório de Cobertura..."
php artisan test --coverage-text

echo "✅ Testes Concluídos!"
echo "📁 Relatórios salvos em: tests/reports/"
echo "🌐 Abra tests/reports/coverage-all/index.html para ver a cobertura de código"
