# 🔐 Configuração de OAuth com Google - Amigos Para Sempre

## 📋 Visão Geral

Este documento contém instruções detalhadas sobre como obter as credenciais necessárias para configurar a autenticação OAuth com Google e como adicioná-las ao arquivo `.env` do projeto.

## 🎯 O que você precisa

Para configurar o OAuth com Google, você precisará de:

1. **GOOGLE_CLIENT_ID**: ID do cliente OAuth
2. **GOOGLE_CLIENT_SECRET**: Chave secreta do cliente OAuth
3. **GOOGLE_REDIRECT_URI**: URI de redirecionamento após autenticação

## 📝 Passo a Passo: Como Obter as Credenciais

### 1. Acessar o Google Cloud Console

1. Acesse o [Google Cloud Console](https://console.cloud.google.com/)
2. Faça login com sua conta Google
3. Se você ainda não tem um projeto, crie um novo:
   - Clique no seletor de projetos no topo da página
   - Clique em **"Novo Projeto"** (ou **"New Project"**)
   - Dê um nome ao projeto (ex: "Amigos Para Sempre")
   - Clique em **"Criar"** (ou **"Create"**)

### 2. Ativar a API do Google+

1. No menu lateral, vá em **"APIs e Serviços"** → **"Biblioteca"** (ou **"APIs & Services"** → **"Library"**)
2. Procure por **"Google+ API"** ou **"Google Identity"**
3. Clique na API e depois em **"Ativar"** (ou **"Enable"**)
   - **Nota**: O Google+ API foi descontinuado, mas você pode usar a **"Google Identity API"** ou simplesmente pular esta etapa, pois o Laravel Socialite gerencia isso automaticamente

### 3. Configurar a Tela de Consentimento OAuth

1. No menu lateral, vá em **"APIs e Serviços"** → **"Tela de consentimento OAuth"** (ou **"OAuth consent screen"**)
2. Selecione o tipo de usuário:
   - **Externo**: Para usuários de qualquer organização Google
   - **Interno**: Apenas para usuários da sua organização (requer Google Workspace)
3. Preencha as informações obrigatórias:
   - **Nome do aplicativo**: "Amigos Para Sempre" (ou o nome que preferir)
   - **Email de suporte do usuário**: Seu email de contato
   - **Logo do aplicativo**: (Opcional) Faça upload de um logo
   - **Domínio de suporte do desenvolvedor**: (Opcional) Seu domínio
   - **Email de contato do desenvolvedor**: Seu email
4. Clique em **"Salvar e continuar"** (ou **"Save and Continue"**)
5. Na seção **"Escopos"** (Scopes):
   - Clique em **"Adicionar ou remover escopos"** (ou **"Add or Remove Scopes"**)
   - Selecione os escopos necessários:
     - `userinfo.email` - Ver seu endereço de email
     - `userinfo.profile` - Ver suas informações básicas de perfil
   - Clique em **"Atualizar"** (ou **"Update"**)
   - Clique em **"Salvar e continuar"** (ou **"Save and Continue"**)
6. Na seção **"Usuários de teste"** (Test users):
   - Se o app estiver em modo de teste, adicione emails de teste que poderão usar o OAuth
   - Para produção, você precisará solicitar verificação do Google
   - Clique em **"Salvar e continuar"** (ou **"Save and Continue"**)
7. Revise as informações e clique em **"Voltar ao painel"** (ou **"Back to Dashboard"**)

### 4. Criar Credenciais OAuth 2.0

1. No menu lateral, vá em **"APIs e Serviços"** → **"Credenciais"** (ou **"Credentials"**)
2. Clique em **"+ Criar credenciais"** (ou **"+ Create Credentials"**)
3. Selecione **"ID do cliente OAuth"** (ou **"OAuth client ID"**)
4. Se for a primeira vez, você precisará configurar a tela de consentimento (se ainda não fez)
5. Configure o tipo de aplicativo:
   - Selecione **"Aplicativo da Web"** (ou **"Web application"**)
6. Preencha os campos:
   - **Nome**: "Amigos Para Sempre Web Client" (ou o nome que preferir)
   - **URIs de redirecionamento autorizados**:
     - Para desenvolvimento local: `http://localhost:8000/auth/google/callback`
     - Para produção: `https://seudominio.com/auth/google/callback`
     - **Importante**: Adicione todas as URLs onde sua aplicação será executada
7. Clique em **"Criar"** (ou **"Create"**)

### 5. Copiar as Credenciais

Após criar as credenciais, uma janela será exibida com:

- **ID do cliente** (Client ID): Esta é sua `GOOGLE_CLIENT_ID`
- **Chave secreta do cliente** (Client secret): Esta é sua `GOOGLE_CLIENT_SECRET`

**⚠️ IMPORTANTE**: 
- Copie essas informações imediatamente, pois a chave secreta só será exibida uma vez
- Se você perder a chave secreta, precisará criar uma nova credencial

### 6. Verificar/Editar Credenciais (Opcional)

Se precisar ver ou editar as credenciais depois:

1. Vá em **"APIs e Serviços"** → **"Credenciais"**
2. Clique no nome da credencial criada
3. Você poderá ver o **Client ID** novamente
4. Se precisar de uma nova **Client Secret**, clique em **"Redefinir chave secreta"** (ou **"Reset secret"**)

## 🔧 Configuração no Arquivo .env

### 1. Localizar o Arquivo .env

O arquivo `.env` está na raiz do projeto Laravel. Se não existir, copie o arquivo `.env.example`:

```bash
cp .env.example .env
```

### 2. Adicionar as Variáveis

Abra o arquivo `.env` e adicione as seguintes linhas na seção de configurações OAuth:

```env
# Configuração OAuth Google
GOOGLE_CLIENT_ID=seu_client_id_aqui.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-sua_chave_secreta_aqui
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

### 3. Configurar para Diferentes Ambientes

#### Desenvolvimento Local

```env
GOOGLE_CLIENT_ID=seu_client_id_aqui.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-sua_chave_secreta_aqui
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

#### Produção

```env
GOOGLE_CLIENT_ID=seu_client_id_aqui.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-sua_chave_secreta_aqui
GOOGLE_REDIRECT_URI=https://seudominio.com/auth/google/callback
```

**⚠️ IMPORTANTE**: 
- Certifique-se de que a `GOOGLE_REDIRECT_URI` corresponde exatamente à URL configurada no Google Cloud Console
- A URL deve incluir o protocolo (`http://` ou `https://`)
- Não adicione barra no final (`/`) a menos que esteja configurado assim no Google Console

## ✅ Verificação da Configuração

### 1. Verificar o Arquivo de Configuração

O Laravel já está configurado para usar essas variáveis. Verifique o arquivo `config/services.php`:

```38:42:config/services.php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),
],
```

### 2. Limpar o Cache de Configuração

Após adicionar as variáveis no `.env`, limpe o cache do Laravel:

```bash
php artisan config:clear
php artisan cache:clear
```

### 3. Testar a Autenticação

1. Acesse a página de login da aplicação
2. Clique no botão "Entrar com Google"
3. Você será redirecionado para a página de autenticação do Google
4. Após autorizar, você será redirecionado de volta para a aplicação

## 🔒 Segurança

### Boas Práticas

1. **Nunca commite o arquivo `.env`** no controle de versão
2. **Use credenciais diferentes** para desenvolvimento e produção
3. **Mantenha as chaves secretas seguras** e não as compartilhe
4. **Revise periodicamente** as credenciais no Google Cloud Console
5. **Use HTTPS em produção** para proteger as credências durante a transmissão

### Rotação de Credenciais

Se suspeitar que suas credenciais foram comprometidas:

1. Acesse o Google Cloud Console
2. Vá em **"APIs e Serviços"** → **"Credenciais"**
3. Clique na credencial comprometida
4. Clique em **"Redefinir chave secreta"** (ou **"Reset secret"**)
5. Atualize o arquivo `.env` com a nova chave
6. Limpe o cache: `php artisan config:clear`

## 🐛 Solução de Problemas

### Erro: "redirect_uri_mismatch"

**Causa**: A URI de redirecionamento no `.env` não corresponde à configurada no Google Console.

**Solução**:
1. Verifique a URL exata no Google Cloud Console
2. Certifique-se de que não há barras extras ou diferenças de protocolo
3. Atualize o `.env` com a URL exata

### Erro: "invalid_client"

**Causa**: Client ID ou Client Secret incorretos.

**Solução**:
1. Verifique se copiou corretamente as credenciais
2. Certifique-se de que não há espaços extras
3. Limpe o cache: `php artisan config:clear`

### Erro: "access_denied"

**Causa**: O usuário negou a permissão ou o app está em modo de teste.

**Solução**:
1. Se o app está em modo de teste, adicione o email do usuário como usuário de teste
2. Para produção, solicite a verificação do app no Google

### OAuth não funciona após mudanças

**Solução**:
1. Limpe todos os caches:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   ```
2. Reinicie o servidor se estiver rodando
3. Verifique se as variáveis estão corretas no `.env`

## 📚 Recursos Adicionais

- [Documentação do Laravel Socialite](https://laravel.com/docs/socialite)
- [Google OAuth 2.0 Documentation](https://developers.google.com/identity/protocols/oauth2)
- [Google Cloud Console](https://console.cloud.google.com/)

## 🚀 Status

**✅ CONFIGURADO E PRONTO PARA USO!**

Após seguir estas instruções, a autenticação OAuth com Google estará funcionando em sua aplicação.

