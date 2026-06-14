# Docker

Este setup sobe a aplicação com PHP-FPM, Nginx e PostgreSQL.

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

4. Gere a chave da aplicação e rode as migrations:

```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
```

5. Para desenvolvimento com Vite dentro do Docker, suba também o serviço `node`:

```bash
docker compose --profile dev up -d node
```

6. Acesse:

```text
http://localhost:8081
```

## Uso em servidor

No servidor, configure o `.env` com `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` apontando para o domínio real e senhas fortes para PostgreSQL.

Depois rode:

```bash
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --force
```

Se quiser que migrations rodem automaticamente ao subir o container, defina:

```env
RUN_MIGRATIONS=true
```

Para atualizar uma nova versão:

```bash
docker compose up -d --build
docker compose exec app php artisan migrate --force
```

## Observações

- O build do frontend roda dentro da imagem Docker e é publicado no volume `public` para o Nginx.
- O serviço `worker` processa a fila database e executa o envio de e-mails em background.
- O serviço `node` é opcional e serve para desenvolvimento com Vite em `http://localhost:5173`.
- O banco principal é `propostei` e o banco de testes `propostei_test` é criado na primeira inicialização do volume PostgreSQL.
- A extensão PHP `gd` está instalada na imagem, então PDFs com imagem também funcionam no container.
- As extensões essenciais para Laravel e PostgreSQL, como `mbstring`, `pdo_pgsql` e `intl`, já estão incluídas na imagem.
- Os volumes `postgres`, `public`, `storage` e `cache` persistem dados entre recriações dos containers.

## Admin

Para liberar a área `/admin/premium-interests` para um usuário:

```bash
docker compose exec app php artisan tinker
```

```php
$user = App\Models\User::where('email', 'seu@email.com')->first();
$user->update(['is_admin' => true]);
```
