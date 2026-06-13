# Docker

Este setup sobe a aplicação com PHP-FPM, Nginx e PostgreSQL.

## Uso local

1. Copie o arquivo de ambiente:

```bash
cp .env.example .env
```

2. Ajuste as variáveis principais no `.env`:

```env
APP_URL=http://localhost:8080
APP_PORT=8080
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=propostei
DB_USERNAME=propostei
DB_PASSWORD=secret
```

3. Suba os containers:

```bash
docker compose up -d --build
```

4. Gere a chave da aplicação e rode as migrations:

```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

5. Acesse:

```text
http://localhost:8080
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
- A extensão PHP `gd` está instalada na imagem, então PDFs com imagem também funcionam no container.
- As extensões essenciais para Laravel e PostgreSQL, como `mbstring`, `pdo_pgsql` e `intl`, já estão incluídas na imagem.
- Os volumes `postgres`, `public`, `storage` e `cache` persistem dados entre recriações dos containers.
