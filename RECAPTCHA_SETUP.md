# Configuração do Google reCAPTCHA

Este documento explica como obter e configurar as chaves do Google reCAPTCHA para proteger os formulários de registro e contato.

## Como obter as chaves do Google reCAPTCHA

### Passo 1: Acessar o Google reCAPTCHA Admin Console

1. Acesse o site: https://www.google.com/recaptcha/admin/create
2. Faça login com sua conta Google

### Passo 2: Registrar um novo site

1. Clique em **"+"** ou **"Criar"** para adicionar um novo site
2. Preencha o formulário:
   - **Rótulo**: Dê um nome para identificar seu site (ex: "Sintonia de Amor")
   - **Tipo de reCAPTCHA**: Escolha uma das opções:
     - **reCAPTCHA v2**: "Não sou um robô" - Checkbox (recomendado para formulários)
     - **reCAPTCHA v2**: Invisível (aparece apenas quando necessário)
     - **reCAPTCHA v3**: Sem interação do usuário (baseado em score)
   - **Domínios**: Adicione os domínios onde o reCAPTCHA será usado:
     - Para desenvolvimento local: `localhost`
     - Para produção: `sintoniadeamor.com.br` e `www.sintoniadeamor.com.br`
   - Aceite os **Termos de Serviço** do Google
   - Clique em **Enviar**

### Passo 3: Obter as chaves

Após criar o site, você receberá duas chaves:

1. **Chave do site (Site Key)**: Chave pública usada no frontend
2. **Chave secreta (Secret Key)**: Chave privada usada no backend para validação

⚠️ **IMPORTANTE**: Mantenha a chave secreta em segurança e nunca a exponha no código frontend!

## Configuração no arquivo .env

Adicione as seguintes variáveis no seu arquivo `.env`:

```env
# Google reCAPTCHA Configuration
RECAPTCHA_SITE_KEY=sua_chave_do_site_aqui
RECAPTCHA_SECRET_KEY=sua_chave_secreta_aqui
RECAPTCHA_VERSION=v2
RECAPTCHA_MIN_SCORE=0.5
```

### Explicação das variáveis:

- **RECAPTCHA_SITE_KEY**: A chave pública do site (Site Key)
- **RECAPTCHA_SECRET_KEY**: A chave secreta (Secret Key)
- **RECAPTCHA_VERSION**: Versão do reCAPTCHA (`v2` ou `v3`)
  - `v2`: Mostra o checkbox "Não sou um robô"
  - `v3`: Validação invisível baseada em score
- **RECAPTCHA_MIN_SCORE**: Score mínimo para aprovação (apenas para v3, padrão: 0.5)
  - Varia de 0.0 a 1.0
  - 1.0 = muito provável que seja humano
  - 0.0 = muito provável que seja bot

## Exemplo de configuração

```env
# Exemplo (substitua pelos seus valores reais)
RECAPTCHA_SITE_KEY=6LdXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
RECAPTCHA_SECRET_KEY=6LdXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
RECAPTCHA_VERSION=v2
RECAPTCHA_MIN_SCORE=0.5
```

## Testando a configuração

### Ambiente de desenvolvimento

Para testar localmente, você precisa adicionar `localhost` nos domínios permitidos no console do Google reCAPTCHA.

### Ambiente de produção

Certifique-se de adicionar todos os domínios onde o site estará disponível:
- `sintoniadeamor.com.br`
- `www.sintoniadeamor.com.br`
- Qualquer subdomínio usado

## Troubleshooting

### Erro: "Invalid site key"
- Verifique se a `RECAPTCHA_SITE_KEY` está correta no `.env`
- Certifique-se de que o domínio está registrado no console do Google

### Erro: "Invalid secret key"
- Verifique se a `RECAPTCHA_SECRET_KEY` está correta no `.env`
- Certifique-se de que está usando a chave secreta (não a chave do site)

### reCAPTCHA não aparece
- Verifique se o script do Google está sendo carregado corretamente
- Verifique o console do navegador para erros JavaScript
- Certifique-se de que o domínio está na lista de domínios permitidos

## Segurança

- ⚠️ **NUNCA** commite o arquivo `.env` no repositório
- ⚠️ **NUNCA** exponha a chave secreta no código frontend
- ⚠️ Mantenha as chaves seguras e não as compartilhe publicamente

## Mais informações

- Documentação oficial: https://developers.google.com/recaptcha
- Console Admin: https://www.google.com/recaptcha/admin

