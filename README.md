# Amigos Para Sempre

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
- **Pagamentos**: Stripe (em desenvolvimento)

## 📋 Instalação

1. Clone o repositório
2. Instale as dependências: `composer install`
3. Configure o arquivo `.env`
4. Execute as migrations: `php artisan migrate`
5. Inicie o servidor: `php artisan serve`

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
