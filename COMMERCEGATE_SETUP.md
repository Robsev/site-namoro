# Configuração do CommerceGate

## Variáveis de Ambiente Necessárias

Adicione as seguintes variáveis ao seu arquivo `.env`:

### Configuração Básica

```env
# ============================================
# COMMERCEGATE CONFIGURATION
# ============================================

# Modo de assinaturas: 'commercegate', 'stripe' (legacy), ou 'mock'
SUBSCRIPTIONS_MODE=commercegate

# Credenciais do CommerceGate
COMMERCEGATE_MERCHANT_ID=104675-TEST
COMMERCEGATE_WEBSITE_ID=31052-TEST
COMMERCEGATE_AUTH_LOGIN=104675-TEST
COMMERCEGATE_AUTH_PASSWORD=XX0e909e2119c04428fxx940

# Modo de teste (true = sandbox/teste, false = produção)
COMMERCEGATE_TEST_MODE=true
```

## Credenciais de Teste vs Produção

### Ambiente de Teste (Sandbox)
- **Merchant ID**: `104675-TEST`
- **Website ID**: `31052-TEST`
- **Auth Login**: `104675-TEST`
- **Auth Password**: `XX0e909e2119c04428fxx940` (fornecido pelo CommerceGate)
- **Test Mode**: `true`

### Ambiente de Produção
Quando você receber suas credenciais de produção do CommerceGate, atualize:

```env
COMMERCEGATE_MERCHANT_ID=seu-merchant-id-producao
COMMERCEGATE_WEBSITE_ID=seu-website-id-producao
COMMERCEGATE_AUTH_LOGIN=seu-auth-login-producao
COMMERCEGATE_AUTH_PASSWORD=sua-senha-producao
COMMERCEGATE_TEST_MODE=false
```

## Exemplo Completo para .env

```env
# ============================================
# APLICAÇÃO
# ============================================
APP_NAME="Amigos Para Sempre"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

# ============================================
# BANCO DE DADOS
# ============================================
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=amigosparasempre
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

# ============================================
# COMMERCEGATE - ASSINATURAS
# ============================================
SUBSCRIPTIONS_MODE=commercegate
COMMERCEGATE_MERCHANT_ID=104675-TEST
COMMERCEGATE_WEBSITE_ID=31052-TEST
COMMERCEGATE_AUTH_LOGIN=104675-TEST
COMMERCEGATE_AUTH_PASSWORD=XX0e909e2119c04428fxx940
COMMERCEGATE_TEST_MODE=true

# ============================================
# OUTRAS CONFIGURAÇÕES
# ============================================
# ... outras configurações do seu .env
```

## Verificação

Após configurar, você pode verificar se está funcionando:

1. **Limpar cache de configuração:**
   ```bash
   php artisan config:clear
   php artisan config:cache
   ```

2. **Verificar configuração via Tinker:**
   ```bash
   php artisan tinker
   ```
   ```php
   config('services.commercegate.merchant_id');
   config('services.commercegate.test_mode');
   config('services.subscriptions.mode');
   ```

## URLs do CommerceGate

### URL Base da API

Conforme a documentação Swagger do CommerceGate, a URL base da API é:

- **Produção**: `https://gw.cgpaytech.com`
- **Teste/Sandbox**: `https://gw.cgpaytech.com` (mesma URL, diferença é nas credenciais)

### Configuração de URLs (Opcional - se não configuradas, será usada a URL padrão)

```env
# URL da API CommerceGate (conforme Swagger)
# NOTA: Teste e produção usam a mesma URL base, a diferença está nas credenciais
COMMERCEGATE_API_URL_TEST=https://gw.cgpaytech.com
COMMERCEGATE_API_URL_PRODUCTION=https://gw.cgpaytech.com
```

**IMPORTANTE**: 
- O CommerceGate usa **Bearer Token** para autenticação (obtido via `/v1/token`)
- O sistema automaticamente obtém o token quando necessário
- Não é necessário configurar URL de formulário hospedado, pois o endpoint `/v1/api/payment_form/configure` retorna a `forwardUrl` automaticamente

### Como obter as URLs corretas

1. **Acesse o portal do merchant do CommerceGate**
2. **Procure na seção "Integration" ou "API Documentation"**
3. **Ou entre em contato com o suporte técnico** para obter:
   - URL exata do formulário de pagamento hospedado
   - Endpoints corretos da API REST
   - Credenciais específicas do ambiente

## Webhook URL

Configure no painel do CommerceGate a seguinte URL de webhook:

- **Desenvolvimento**: `https://seu-dominio.dev/commercegate/webhook`
- **Produção**: `https://seu-dominio.com/commercegate/webhook`

A rota está configurada em `routes/web.php` como `commercegate.webhook` e **NÃO** requer autenticação CSRF (está fora do middleware `auth`).

## Segurança

⚠️ **IMPORTANTE:**
- Nunca commite o arquivo `.env` no Git
- As credenciais de produção devem ser mantidas em segredo
- Use variáveis de ambiente diferentes para desenvolvimento e produção
- Rotacione as senhas periodicamente

## Suporte

Para obter credenciais de produção ou suporte técnico, entre em contato com o CommerceGate através do seu portal de cliente.

