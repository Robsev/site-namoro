#!/usr/bin/env node

/**
 * Script para configurar Husky e criar o pre-commit hook
 * Executado automaticamente após npm install via script "prepare"
 */

import { execSync } from 'child_process';
import { existsSync, mkdirSync, writeFileSync, chmodSync } from 'fs';
import { fileURLToPath } from 'url';
import { dirname, resolve } from 'path';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);
const projectRoot = resolve(__dirname, '..');
const huskyDir = resolve(projectRoot, '.husky');

try {
    // Criar diretório .husky se não existir
    if (!existsSync(huskyDir)) {
        mkdirSync(huskyDir, { recursive: true });
    }

    // Criar diretório .husky/_ se não existir
    const huskyUnderscoreDir = resolve(huskyDir, '_');
    if (!existsSync(huskyUnderscoreDir)) {
        mkdirSync(huskyUnderscoreDir, { recursive: true });
    }

    // Criar arquivo husky.sh se não existir
    const huskyShPath = resolve(huskyUnderscoreDir, 'husky.sh');
    if (!existsSync(huskyShPath)) {
        const huskyShContent = `#!/usr/bin/env sh
if [ -z "$husky_skip_init" ]; then
  debug () {
    if [ "$HUSKY_DEBUG" = "1" ]; then
      echo "husky (debug) - $1"
    fi
  }

  readonly hook_name="$(basename -- "$0")"
  debug "starting $hook_name..."

  if [ "$HUSKY" = "0" ]; then
    debug "HUSKY env variable is set to 0, skipping hook"
    exit 0
  fi

  if [ -f ~/.huskyrc ]; then
    debug "sourcing ~/.huskyrc"
    . ~/.huskyrc
  fi

  readonly husky_skip_init=1
  export husky_skip_init
  sh -e "$0" "$@"
  exitcode="$?"

  if [ $exitcode != 0 ]; then
    echo "husky - $hook_name hook exited with code $exitcode (error)"
  fi

  if [ $exitcode = 127 ]; then
    echo "husky - command not found in PATH=$PATH"
  fi

  exit $exitcode
fi
`;
        writeFileSync(huskyShPath, huskyShContent);
        try {
            chmodSync(huskyShPath, 0o755);
        } catch (err) {
            // Ignorar erro no Windows
        }
    }

    // Criar pre-commit hook
    const preCommitHook = `#!/usr/bin/env sh
. "$(dirname -- "$0")/_/husky.sh"

# Executar verificação de mudanças no frontend
node scripts/check-frontend-changes.js
`;

    const hookPath = resolve(huskyDir, 'pre-commit');
    writeFileSync(hookPath, preCommitHook);
    
    // Tornar o hook executável (Unix/Linux/Mac)
    try {
        chmodSync(hookPath, 0o755);
    } catch (err) {
        // Ignorar erro no Windows (chmod não funciona)
    }

    console.log('✅ Git hook configurado com sucesso!');
    console.log('📝 O build será executado automaticamente quando você modificar arquivos do frontend.');
} catch (error) {
    console.error('⚠️  Aviso: Não foi possível configurar o git hook:', error.message);
    console.log('💡 Você pode executar manualmente: ./build-local.sh');
}

