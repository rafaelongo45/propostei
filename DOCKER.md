# Docker

Este setup sobe a aplicação com Apache + PHP em um container, PostgreSQL em outro e um worker opcional para filas.

## Uso local

1. Copie o arquivo de ambiente:

```bash
cp .env.example .env
```

2. Ajuste as variáveis principais no `.env`:

```env
APP_URL=http://localhost:8081
APP_PORT=8081
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_FORWARD_PORT=5433
DB_DATABASE=propostei
DB_USERNAME=propostei
DB_PASSWORD=propostei
```

3. Suba os containers:

```bash
docker compose up -d --build
```

4. Gere a chave, se ainda não existir, e rode seeders se desejar:

```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan db:seed
```

As migrations rodam automaticamente no start do container via `docker/start.sh`.

5. Para desenvolvimento com Vite dentro do Docker, suba também o serviço `node`:

```bash
docker compose --profile dev up -d node
```

6. Acesse:

```text
http://localhost:8081
```

## Render

O `Dockerfile` usa Apache e respeita a variável `PORT` fornecida pelo Render, com fallback `10000`.

No start do container, `docker/start.sh` executa:

```bash
php artisan migrate --force
php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

O container inicia com:

```bash
apache2-foreground
```

## Observações

- O build do frontend roda dentro da imagem Docker.
- O serviço `worker` processa a fila database e executa o envio de e-mails em background.
- O serviço `node` é opcional e serve para desenvolvimento com Vite em `http://localhost:5173`.
- O banco principal é `propostei` e o banco de testes `propostei_test` é criado na primeira inicialização do volume PostgreSQL.
- A extensão PHP `gd` está instalada na imagem, então PDFs com imagem também funcionam no container.
- As extensões essenciais para Laravel e PostgreSQL, como `mbstring`, `pdo_pgsql` e `intl`, já estão incluídas na imagem.
- Os volumes `postgres`, `storage` e `cache` persistem dados entre recriações dos containers.

## Admin

Para liberar a área `/admin/premium-interests` para um usuário:

```bash
docker compose exec app php artisan tinker
```

```php
$user = App\Models\User::where('email', 'seu@email.com')->first();
$user->update(['is_admin' => true]);
```
