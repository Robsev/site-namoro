# 🚀 Guia de Deploy - Amigos Para Sempre

Este guia explica como fazer deploy do "Amigos Para Sempre" em produção.

## 📋 Pré-requisitos

### Servidor
- **PHP 8.2+** com extensões: BCMath, Ctype, cURL, DOM, Fileinfo, JSON, Mbstring, OpenSSL, PCRE, PDO, Tokenizer, XML, GD, MySQL
- **Composer** 2.0+
- **Node.js** 18+ e **npm** 9+
- **MySQL** 8.0+ ou **MariaDB** 10.6+
- **Redis** (opcional, para cache e sessões)
- **Nginx** ou **Apache** com mod_rewrite

### Domínio e SSL
- Domínio configurado
- Certificado SSL (Let's Encrypt recomendado)

## 🛠️ Scripts de Deploy

### 1. Deploy Completo
```bash
./deploy.sh
```
**O que faz:**
- Instala dependências PHP e Node.js
- Executa build do frontend com Vite
- Configura cache de produção
- Executa migrations do banco
- Configura permissões e storage
- Otimiza performance

### 2. Build Rápido (Apenas Frontend)
```bash
./build.sh
```
**O que faz:**
- Instala dependências Node.js
- Executa build do frontend
- Verifica se os arquivos foram gerados

## 📝 Passo a Passo Manual

### 1. Preparar Servidor
```bash
# Atualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar dependências
sudo apt install nginx mysql-server redis-server php8.2-fpm php8.2-mysql php8.2-xml php8.2-gd php8.2-curl php8.2-mbstring php8.2-zip php8.2-bcmath php8.2-redis

# Instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Instalar Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs
```

### 2. Configurar Banco de Dados
```bash
# Acessar MySQL
sudo mysql -u root -p

# Criar banco e usuário
CREATE DATABASE amigos_para_sempre_prod;
CREATE USER 'amigos_user'@'localhost' IDENTIFIED BY 'senha_forte_aqui';
GRANT ALL PRIVILEGES ON amigos_para_sempre_prod.* TO 'amigos_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Deploy da Aplicação
```bash
# Clonar repositório
git clone https://github.com/SEU_USUARIO/amigosparasempre.git
cd amigosparasempre

# Executar deploy
./deploy.sh
```

### 4. Configurar Nginx
```nginx
server {
    listen 80;
    listen 443 ssl http2;
    server_name seu-dominio.com www.seu-dominio.com;
    root /var/www/amigosparasempre/public;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/seu-dominio.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/seu-dominio.com/privkey.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 5. Configurar SSL (Let's Encrypt)
```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-nginx

# Obter certificado
sudo certbot --nginx -d seu-dominio.com -d www.seu-dominio.com

# Testar renovação automática
sudo certbot renew --dry-run
```

## ⚙️ Configurações de Produção

### 1. Arquivo .env
```bash
# Copiar arquivo de exemplo
cp .env.production .env

# Editar configurações
nano .env
```

**Configurações importantes:**
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://seu-dominio.com`
- Configurar banco de dados
- Configurar OAuth (Google)
- Configurar Stripe (pagamentos)

### 2. Configurar Cache Redis
```bash
# Editar /etc/redis/redis.conf
sudo nano /etc/redis/redis.conf

# Configurar:
maxmemory 256mb
maxmemory-policy allkeys-lru

# Reiniciar Redis
sudo systemctl restart redis-server
```

### 3. Configurar Queue Worker
```bash
# Criar supervisor config
sudo nano /etc/supervisor/conf.d/amigos-queue.conf

# Conteúdo:
[program:amigos-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/amigosparasempre/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/amigosparasempre/storage/logs/queue-worker.log
stopwaitsecs=3600

# Recarregar supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start amigos-queue:*
```

## 🔧 Comandos de Manutenção

### Build do Frontend
```bash
# Build completo
npm run build

# Build em modo desenvolvimento
npm run dev

# Instalar dependências
npm ci --only=production
```

### Cache e Otimização
```bash
# Limpar todos os caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Reconfigurar cache de produção
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Banco de Dados
```bash
# Executar migrations
php artisan migrate --force

# Executar seeders
php artisan db:seed --force

# Backup do banco
mysqldump -u amigos_user -p amigos_para_sempre_prod > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Manutenção
```bash
# Ativar modo de manutenção
php artisan maintenance on --ip=SEU_IP

# Desativar modo de manutenção
php artisan maintenance off

# Verificar status
php artisan about
```

## 📊 Monitoramento

### Logs
```bash
# Logs da aplicação
tail -f storage/logs/laravel.log

# Logs do Nginx
sudo tail -f /var/log/nginx/access.log
sudo tail -f /var/log/nginx/error.log

# Logs do PHP-FPM
sudo tail -f /var/log/php8.2-fpm.log
```

### Performance
```bash
# Verificar uso de memória
free -h

# Verificar uso de CPU
top

# Verificar espaço em disco
df -h

# Verificar processos PHP
ps aux | grep php
```

## 🚨 Troubleshooting

### Problemas Comuns

**1. Erro 500 - Internal Server Error**
```bash
# Verificar logs
tail -f storage/logs/laravel.log

# Verificar permissões
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

**2. Assets não carregam**
```bash
# Verificar se o build foi executado
ls -la public/build/

# Executar build
npm run build

# Verificar link simbólico do storage
php artisan storage:link
```

**3. Banco de dados não conecta**
```bash
# Testar conexão
php artisan tinker
>>> DB::connection()->getPdo();

# Verificar configurações
php artisan config:show database
```

**4. Cache não funciona**
```bash
# Limpar cache
php artisan cache:clear

# Verificar configuração do Redis
redis-cli ping
```

## 🔄 Atualizações

### Deploy de Atualizações
```bash
# Fazer backup
cp .env .env.backup

# Atualizar código
git pull origin main

# Executar deploy
./deploy.sh

# Verificar se está funcionando
curl -I https://seu-dominio.com
```

### Rollback
```bash
# Voltar para commit anterior
git reset --hard HEAD~1

# Executar deploy
./deploy.sh
```

## 📞 Suporte

Em caso de problemas:
1. Verificar logs em `storage/logs/laravel.log`
2. Verificar status dos serviços: `sudo systemctl status nginx php8.2-fpm redis-server`
3. Verificar configurações: `php artisan about`
4. Contatar suporte técnico

---

**🎉 Deploy concluído com sucesso! Amigos Para Sempre está online! ❤️**
