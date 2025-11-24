# Sintonia de Amor

Sistema de relacionamento entre amigos desenvolvido em Laravel 12.

## 🚀 Funcionalidades

- **Autenticação OAuth**: Login com Google e Microsoft
- **Registro Tradicional**: Cadastro com e-mail e senha
- **Sistema de Perfis**: Informações detalhadas dos usuários
- **Matching Inteligente**: Algoritmo de compatibilidade
- **Sistema de Assinaturas**: Planos gratuito e premium
- **Interface Moderna**: Design responsivo com Tailwind CSS

## 🛠️ Tecnologias

- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Blade Templates, Tailwind CSS
- **Banco de Dados**: MySQL
- **Autenticação**: Laravel Socialite (Google, Microsoft)
- **Pagamentos**: CommerceGate (assinaturas)

## 📋 Instalação

1. Clone o repositório
2. Instale as dependências PHP: `composer install`
3. Instale as dependências Node.js: `npm install` (configura hooks automáticos)
4. Configure o arquivo `.env`
5. Execute as migrations: `php artisan migrate`
6. Inicie o servidor: `php artisan serve`

### 🎨 Build do Frontend

O build do frontend é executado **automaticamente** quando você faz commit de mudanças nos arquivos CSS/JS. 

Se precisar executar manualmente:
```bash
./build-local.sh
```

Veja mais detalhes em [BUILD_ASSETS.md](BUILD_ASSETS.md)

## 🔧 Configuração

Configure as credenciais OAuth no arquivo `.env`:

```env
GOOGLE_CLIENT_ID=seu_google_client_id
GOOGLE_CLIENT_SECRET=seu_google_client_secret
MICROSOFT_CLIENT_ID=seu_microsoft_client_id
MICROSOFT_CLIENT_SECRET=seu_microsoft_client_secret
```

## 📄 Licença

Este projeto está licenciado sob a [MIT License](LICENSE).
