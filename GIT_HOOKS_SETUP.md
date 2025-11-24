# 🔧 Configuração de Git Hooks para Build Automático

Este projeto usa **Git Hooks** para executar o build do frontend automaticamente sempre que você modificar arquivos CSS/JS e fizer commit.

## ⚡ Como Funciona

Quando você modifica arquivos do frontend e faz commit, o sistema:
1. Detecta automaticamente as mudanças
2. Executa `npm run build`
3. Adiciona os arquivos de build ao commit
4. Continua com o commit normalmente

## 🚀 Configuração Inicial

### Opção 1: Automática (Recomendada)

Execute `npm install` e os hooks serão configurados automaticamente:

```bash
npm install
```

O script `prepare` no `package.json` executa automaticamente após a instalação.

### Opção 2: Manual

Se por algum motivo os hooks não foram configurados:

```bash
# No Linux/Mac
./scripts/install-hooks.sh

# No Windows (via Git Bash ou WSL)
bash scripts/install-hooks.sh
```

Ou execute diretamente:

```bash
node scripts/setup-husky.js
```

## 📋 Arquivos Monitorados

O sistema monitora mudanças em:
- `resources/css/` - Arquivos CSS
- `resources/js/` - Arquivos JavaScript
- `vite.config.js` - Configuração do Vite
- `package.json` - Dependências Node.js
- `tailwind.config.js` - Configuração do Tailwind
- `postcss.config.js` - Configuração do PostCSS

## ✅ Verificar se Está Funcionando

Para testar se os hooks estão configurados:

```bash
# Verificar se o hook existe
ls -la .husky/pre-commit

# Fazer uma mudança de teste em um arquivo CSS
echo "/* teste */" >> resources/css/app.css

# Adicionar ao staging
git add resources/css/app.css

# Tentar fazer commit (o build será executado automaticamente)
git commit -m "test: verificar build automático"

# Reverter a mudança de teste
git reset HEAD~1
git checkout -- resources/css/app.css
```

## 🔧 Desabilitar Temporariamente

Se você precisar fazer commit sem executar o build:

```bash
# Desabilitar hook para um commit específico
git commit --no-verify -m "sua mensagem"

# Ou via variável de ambiente
HUSKY=0 git commit -m "sua mensagem"
```

**⚠️ Atenção:** Lembre-se de executar o build manualmente depois com `./build-local.sh`!

## 🐛 Troubleshooting

### Hook não está executando

1. Verifique se o hook existe:
   ```bash
   ls -la .husky/pre-commit
   ```

2. Verifique se tem permissão de execução (Linux/Mac):
   ```bash
   chmod +x .husky/pre-commit
   chmod +x .husky/_/husky.sh
   ```

3. Reinstale os hooks:
   ```bash
   node scripts/setup-husky.js
   ```

### Erro: "command not found: node"

Certifique-se de que Node.js está instalado e no PATH:
```bash
node --version
npm --version
```

### Build falha durante o commit

Se o build falhar, o commit será cancelado. Corrija os erros e tente novamente.

Para pular o hook temporariamente:
```bash
git commit --no-verify -m "sua mensagem"
```

Depois execute o build manualmente:
```bash
./build-local.sh
git add public/build/
git commit --amend --no-edit
```

## 📚 Arquivos Relacionados

- `.husky/pre-commit` - Hook executado antes do commit
- `scripts/check-frontend-changes.js` - Script que detecta mudanças e executa build
- `scripts/setup-husky.js` - Script de configuração dos hooks
- `package.json` - Contém script `prepare` que configura hooks automaticamente

## 💡 Dicas

- Os hooks são configurados automaticamente quando você clona o repositório e executa `npm install`
- Se você trabalha em equipe, todos devem executar `npm install` após clonar o repositório
- Os hooks são commitados no repositório (`.husky/`), então todos terão a mesma configuração
- Você pode ver o que está sendo executado durante o commit observando a saída no terminal

---

**🎉 Pronto!** Agora você não precisa mais se preocupar em executar o build manualmente. Apenas faça commit normalmente!

