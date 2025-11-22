# Compilação de Assets do Vite

## Problema

Se você está vendo o erro:
```
Vite manifest not found at: /path/to/public/build/manifest.json
```

Isso significa que os assets do Vite não foram compilados para produção.

## Solução Rápida

### Opção 1: Usar o script helper (Recomendado)

```bash
./build-assets.sh
```

### Opção 2: Compilar manualmente

```bash
# 1. Instalar dependências Node.js (se ainda não instalou)
npm install

# 2. Compilar assets
npm run build
```

### Opção 3: Usar o script de deploy completo

O arquivo `deploy.sh` já inclui a compilação de assets automaticamente:

```bash
./deploy.sh
```

## Verificação

Após compilar, verifique se os arquivos foram gerados:

```bash
ls -la public/build/
```

Você deve ver:
- `manifest.json` - Arquivo principal do Vite
- Arquivos CSS e JS compilados

## Nota Importante

O diretório `public/build` está no `.gitignore` porque os assets devem ser compilados em cada ambiente (desenvolvimento e produção). **Nunca faça commit** do diretório `public/build`.

## Em Produção

Em produção, sempre execute o build após:
- Fazer deploy de novo código
- Atualizar dependências Node.js
- Modificar arquivos em `resources/css/` ou `resources/js/`

## Troubleshooting

### Erro: "npm: command not found"

**Opção 1: Instalar Node.js/npm no servidor (Recomendado para servidores dedicados)**

```bash
# Para servidores com acesso root/sudo
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# Verificar instalação
node --version
npm --version
```

**Opção 2: Compilar localmente e fazer upload (Recomendado para hospedagem compartilhada)**

Se você não tem acesso root ou não pode instalar Node.js no servidor:

1. **Compile os assets no seu computador local:**
```bash
# No seu computador local
cd /caminho/para/site-namoro
npm install
npm run build
```

2. **Faça upload do diretório public/build para o servidor:**
```bash
# Usando SCP
scp -r public/build usuario@servidor:/home/u399618994/domains/sintoniadeamor.com.br/site-namoro/public/

# Ou usando SFTP/FTP
# Faça upload da pasta public/build completa
```

3. **Verifique no servidor:**
```bash
ls -la public/build/
```

**Opção 3: Usar NVM (Node Version Manager) sem root**

```bash
# Instalar NVM
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash

# Recarregar shell
source ~/.bashrc

# Instalar Node.js
nvm install 18
nvm use 18

# Verificar
node --version
npm --version
```

### Erro: "Cannot find module"
- Execute `npm install` antes de `npm run build`
- Verifique se `package.json` existe e está correto

### Build falha
- Verifique os logs de erro
- Certifique-se de que todas as dependências estão instaladas
- Verifique se `vite.config.js` está configurado corretamente

