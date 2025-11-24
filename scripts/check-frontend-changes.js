#!/usr/bin/env node

/**
 * Script para verificar se há mudanças no frontend que requerem rebuild
 * Este script é executado automaticamente antes do commit via git hook
 */

import { execSync } from 'child_process';
import { existsSync } from 'fs';
import { fileURLToPath } from 'url';
import { dirname, resolve } from 'path';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);
const projectRoot = resolve(__dirname, '..');

// Arquivos e diretórios que indicam mudanças no frontend
const FRONTEND_PATTERNS = [
    'resources/css/',
    'resources/js/',
    'vite.config.js',
    'package.json',
    'tailwind.config.js',
    'postcss.config.js',
];

// Verificar se estamos em um repositório git
if (!existsSync(resolve(projectRoot, '.git'))) {
    console.log('⚠️  Não é um repositório git, pulando verificação de mudanças no frontend');
    process.exit(0);
}

try {
    // Obter arquivos staged para commit
    const stagedFiles = execSync('git diff --cached --name-only', { 
        encoding: 'utf-8',
        cwd: projectRoot 
    }).trim().split('\n').filter(Boolean);

    // Verificar se algum arquivo do frontend foi modificado
    const hasFrontendChanges = stagedFiles.some(file => 
        FRONTEND_PATTERNS.some(pattern => file.includes(pattern))
    );

    if (hasFrontendChanges) {
        console.log('🎨 Mudanças detectadas no frontend!');
        console.log('📦 Executando build automático...\n');
        
        // Executar build
        execSync('npm run build', { 
            stdio: 'inherit',
            cwd: projectRoot 
        });

        // Verificar se os arquivos de build foram gerados
        const buildManifest = resolve(projectRoot, 'public/build/manifest.json');
        if (!existsSync(buildManifest)) {
            console.error('\n❌ Erro: Build não gerou manifest.json');
            process.exit(1);
        }

        // Adicionar arquivos de build ao staging
        try {
            execSync('git add public/build/', { 
                stdio: 'pipe',
                cwd: projectRoot 
            });
            console.log('\n✅ Arquivos de build adicionados ao commit automaticamente');
        } catch (error) {
            console.warn('\n⚠️  Aviso: Não foi possível adicionar arquivos de build ao git');
            console.warn('   Execute manualmente: git add public/build/');
        }
    } else {
        // Não mostrar mensagem se não houver mudanças (menos ruído)
        // console.log('✓ Nenhuma mudança no frontend detectada');
    }
} catch (error) {
    console.error('❌ Erro ao verificar mudanças no frontend:', error.message);
    process.exit(1);
}

