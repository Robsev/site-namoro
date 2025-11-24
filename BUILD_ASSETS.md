# Compilação de Assets do Vite

## ⚠️ Processo Atualizado

**IMPORTANTE:** O servidor de produção **não suporta** execução do npm. Por isso, o build do frontend deve ser feito **localmente** e os arquivos devem ser **commitados no repositório**.

## 🚀 Processo Recomendado

### ⚡ Build Automático (Recomendado)

**O build é executado automaticamente** quando você faz commit de mudanças no frontend!

O sistema usa **Git Hooks** (via Husky) para detectar mudanças em:
- `resources/css/`
- `resources/js/`
- `vite.config.js`
- `package.json`
- `tailwind.config.js`
- `postcss.config.js`

**Como funciona:**
1. Você modifica arquivos do frontend
2. Você faz `git add` e `git commit`
3. **Automaticamente:** O build é executado e os arquivos são adicionados ao commit
4. Você faz `git push` normalmente

**Configuração inicial (apenas uma vez):**
```bash
# Instalar dependências (inclui Husky)
npm install

# Configurar hooks (executado automaticamente após npm install)
# Ou execute manualmente se necessário:
./scripts/install-hooks.sh
```

### Opção 2: Build Manual com Script Helper

Se preferir executar manualmente ou se o hook automático não funcionar:

```bash
./build-local.sh
```

Este script:
- Instala/atualiza dependências Node.js
- Executa o build do frontend
- Verifica se os arquivos foram gerados
- Prepara os arquivos para commit no git

Depois, faça commit e push:
```bash
git add public/build/
git commit -m "build: atualizar assets do frontend"
git push origin main
```

### Opção 2: Compilar manualmente

```bash
# 1. Instalar dependências Node.js (se ainda não instalou)
npm install

# 2. Compilar assets
npm run build

# 3. Verificar se os arquivos foram gerados
ls -la public/build/

# 4. Adicionar ao git e fazer commit
git add public/build/
git commit -m "build: atualizar assets do frontend"
git push origin main
```

## 📋 Quando Fazer Build

Execute o build local sempre que:
- Modificar arquivos em `resources/css/` ou `resources/js/`
- Atualizar dependências Node.js no `package.json`
- Fizer mudanças que afetem o frontend

## ✅ Verificação

Após compilar, verifique se os arquivos foram gerados:

```bash
ls -la public/build/
```

Você deve ver:
- `manifest.json` - Arquivo principal do Vite
- Arquivos CSS e JS compilados (ex: `app-XXXXX.js`, `app-XXXXX.css`)

## 🔄 Deploy em Produção

No servidor de produção, o script `deploy.sh` **não executa** npm build. Ele apenas:
- Verifica se os arquivos `public/build/` existem
- Garante que estão atualizados (via git pull)

Se os arquivos não existirem, o deploy falhará com uma mensagem clara indicando que você precisa executar o build localmente.

## 📝 Nota Importante

**O diretório `public/build` NÃO está mais no `.gitignore`** porque os arquivos de build devem ser commitados no repositório. Isso permite que o servidor de produção receba os arquivos prontos via `git pull`, sem precisar executar npm.

## 🛠️ Troubleshooting

### Erro: "npm: command not found" (no servidor)

**Isso é esperado!** O servidor de produção não precisa ter npm instalado. Execute o build localmente no seu computador de desenvolvimento.

### Erro: "Diretório public/build não encontrado" (no deploy)

Isso significa que você esqueceu de fazer o build local e commit dos arquivos. Execute:

```bash
# No seu computador local
./build-local.sh
git add public/build/
git commit -m "build: atualizar assets do frontend"
git push origin main

# Depois, no servidor
./deploy.sh
```

### Erro: "Cannot find module"

Execute `npm install` antes de `npm run build` no seu computador local.

### Build falha

- Verifique os logs de erro
- Certifique-se de que todas as dependências estão instaladas (`npm install`)
- Verifique se `vite.config.js` está configurado corretamente
- Verifique se `package.json` existe e está correto

### Arquivos de build não aparecem no git

Verifique se `public/build` foi removido do `.gitignore`. Se ainda estiver lá, remova a linha `/public/build` do arquivo `.gitignore`.

## 🔍 Verificar Status

Para verificar se os arquivos de build estão sendo rastreados pelo git:

```bash
git status public/build/
```

Se aparecerem como "untracked", adicione-os:
```bash
git add public/build/
```

## 📚 Scripts Disponíveis

- `npm install` - Instala dependências e configura hooks automaticamente
- `./scripts/install-hooks.sh` - Configura git hooks manualmente (se necessário)
- `./build-local.sh` - Build local completo com verificação e preparação para commit
- `./deploy.sh` - Deploy completo em produção (não executa npm)
- `npm run build` - Build manual (use apenas localmente)
- `npm run dev` - Modo desenvolvimento com hot-reload (use apenas localmente)

## 🔧 Desabilitar Build Automático (Temporariamente)

Se você precisar fazer commit sem executar o build automaticamente:

```bash
# Desabilitar hook para um commit específico
git commit --no-verify -m "sua mensagem"

# Ou desabilitar temporariamente via variável de ambiente
HUSKY=0 git commit -m "sua mensagem"
```

**⚠️ Atenção:** Use apenas quando necessário. Lembre-se de executar o build manualmente depois!

---

**💡 Dica:** O build automático funciona na maioria dos casos! Apenas faça commit normalmente e os arquivos serão compilados automaticamente.
