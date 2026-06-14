#!/usr/bin/env bash
set -e

PORT="${PORT:-10000}"

echo "Configurando Apache para escutar na porta ${PORT}..."
sed -i -E "s/^Listen [0-9]+/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i -E "s/<VirtualHost \\*:[0-9]+>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

echo "Configurando DocumentRoot para /var/www/html/public..."
sed -i -E "s|DocumentRoot .+|DocumentRoot /var/www/html/public|" /etc/apache2/sites-available/000-default.conf

cat >/etc/apache2/conf-available/laravel.conf <<'EOF'
<Directory /var/www/html/public>
    AllowOverride All
    Require all granted
</Directory>
EOF

a2enconf laravel >/dev/null

echo "Ajustando permissões..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

echo "Limpando caches antigos..."
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

echo "Rodando migrations de produção..."
php artisan migrate --force

echo "Criando storage link, se necessário..."
php artisan storage:link || true

echo "Gerando caches de produção..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Iniciando Apache na porta ${PORT}..."
exec apache2-foreground
