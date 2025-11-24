# Solução: npm command not found

## Problema

Ao tentar executar `npm install` ou `npm run build` no servidor, você recebe:
```
-bash: npm: command not found
```

Isso significa que Node.js/npm não está instalado ou não está no PATH do servidor.

## Soluções

### ✅ Solução 1: Compilar Localmente e Fazer Upload (MAIS RÁPIDA)

**Esta é a solução mais rápida se você não tem acesso root no servidor.**

#### Passo 1: Compilar no seu computador local

```bash
# No seu computador Windows (usando WSL ou Git Bash)
cd "/mnt/c/Users/Jose Carlos/Documents/MEGA/Trabalho/JCDS/Projetos/site-namoro"

# Instalar dependências (se ainda não instalou)
npm install

# Compilar assets
npm run build
```

#### Passo 2: Verificar se o build foi criado

```bash
# Verificar se public/build foi criado
ls -la public/build/

# Você deve ver:
# - manifest.json
# - assets/ (com arquivos CSS e JS)
```

#### Passo 3: Fazer upload para o servidor

**Opção A: Usando SCP (via linha de comando)**

```bash
# No seu computador local
scp -r public/build u399618994@br-asc-web1661:/home/u399618994/domains/sintoniadeamor.com.br/site-namoro/public/
```

**Opção B: Usando SFTP/FTP (via cliente gráfico)**

1. Conecte-se ao servidor via SFTP/FTP
2. Navegue até: `/home/u399618994/domains/sintoniadeamor.com.br/site-namoro/public/`
3. Faça upload da pasta `build` completa (com todos os arquivos dentro)

**Opção C: Usando Git (se você tem acesso)**

```bash
# Temporariamente, adicione public/build ao git (apenas para upload)
# ATENÇÃO: Remova depois!

# No seu computador local
git add public/build
git commit -m "Temporary: adicionar build assets"
git push

# No servidor
git pull

# Depois, remova do git:
git rm -r --cached public/build
git commit -m "Remover build do git"
git push
```

### ✅ Solução 2: Instalar Node.js no Servidor (Requer Acesso Root)

Se você tem acesso root ou sudo no servidor:

```bash
# Instalar Node.js 18.x
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# Verificar instalação
node --version  # Deve mostrar v18.x.x
npm --version    # Deve mostrar v9.x.x ou superior

# Agora você pode executar
cd /home/u399618994/domains/sintoniadeamor.com.br/site-namoro
npm install
npm run build
```

### ✅ Solução 3: Usar NVM (Sem Acesso Root)

Se você não tem acesso root, mas tem acesso ao seu diretório home:

```bash
# Instalar NVM (Node Version Manager)
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash

# Recarregar o shell
source ~/.bashrc

# Instalar Node.js 18
nvm install 18
nvm use 18

# Verificar
node --version
npm --version

# Agora você pode executar
cd /home/u399618994/domains/sintoniadeamor.com.br/site-namoro
npm install
npm run build
```

### ✅ Solução 4: Verificar se Node.js já está instalado em outro local

Às vezes Node.js está instalado, mas não está no PATH:

```bash
# Verificar se node existe em locais comuns
which node
whereis node

# Verificar se está em /usr/local/bin
ls -la /usr/local/bin/node

# Se encontrar, adicionar ao PATH
export PATH=$PATH:/caminho/para/node
```

## Verificação Final

Após qualquer solução, verifique se funcionou:

```bash
# No servidor
cd /home/u399618994/domains/sintoniadeamor.com.br/site-namoro
ls -la public/build/

# Você deve ver:
# - manifest.json
# - assets/ (diretório com arquivos compilados)
```

## Recomendação

**Para hospedagem compartilhada (sem acesso root):**
- Use a **Solução 1** (compilar localmente e fazer upload)

**Para servidor dedicado/VPS (com acesso root):**
- Use a **Solução 2** (instalar Node.js no servidor)

**Para servidor sem root mas com acesso ao home:**
- Use a **Solução 3** (NVM)

## Próximos Passos

Após resolver o problema do npm e compilar os assets:

1. Verifique se o erro do Vite desapareceu
2. Acesse o site e verifique se os estilos CSS estão carregando
3. Verifique o console do navegador para erros JavaScript

## Nota Importante

O diretório `public/build` está no `.gitignore` porque deve ser gerado em cada ambiente. 
**Nunca faça commit permanente** do diretório `public/build` no Git.





