# propostei

MVP comercial para freelancers e pequenos prestadores criarem propostas profissionais, compartilharem um link público, receberem aceite/recusa e baixarem PDF.

## Requisitos

- Docker e Docker Compose
- Node.js e npm, se preferir rodar o Vite fora do Docker
- Composer/PHP local apenas se não for usar Docker

## Setup com Docker

1. Copie o ambiente:

```bash
cp .env.example .env
```

2. Confira as variáveis principais:

```env
APP_URL=http://localhost:8081
APP_PORT=8081
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_FORWARD_PORT=5433
DB_DATABASE=propostei
DB_TEST_DATABASE=propostei_test
DB_USERNAME=propostei
DB_PASSWORD=propostei
```

3. Suba a aplicação:

```bash
docker compose up -d --build
```

4. Prepare o Laravel:

```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan db:seed
```

5. Acesse:

```text
http://localhost:8081
```

Usuário seedado:

```text
test@example.com
password
```

## Vite

O build de produção dos assets roda durante o build da imagem Docker. Para desenvolvimento com hot reload dentro do Docker:

```bash
docker compose --profile dev up -d node
```

Ou rode localmente:

```bash
npm install
npm run dev
```

## Fluxo principal

1. Faça login ou crie uma conta.
2. Edite o perfil e informe o nome da empresa/marca, se houver.
3. Envie um logo em PNG, JPG ou WebP até 2 MB.
4. Cadastre um cliente.
5. Crie uma proposta com itens e valores.
6. Copie o link público, envie por WhatsApp ou envie por e-mail pelo sistema.
7. Abra o link público como cliente e aceite ou recuse.
8. Baixe o PDF da proposta.

O PDF prioriza a identidade do prestador: exibe o logo cadastrado, ou o nome da empresa, ou o nome do usuário. Observações internas e IPs de aceite/recusa não aparecem no PDF nem nas telas públicas.

## E-mail e fila

O envio por e-mail usa Laravel Mail dentro de um Job. Ao clicar em “Enviar por e-mail”, a aplicação registra o pedido, coloca o Job na fila e o worker envia a mensagem. A proposta só muda para `sent` depois que o Job envia o e-mail com sucesso.

No Docker, o serviço `worker` já roda:

```bash
docker compose ps
```

Fora do Docker, rode o worker manualmente:

```bash
php artisan queue:work
```

Configuração local padrão:

```env
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME="${APP_NAME}"
QUEUE_CONNECTION=database
```

Para produção, configure SMTP ou provedor transacional. O remetente deve ser do domínio/sistema, e o e-mail do usuário entra como `Reply-To`, evitando enviar como se fosse o endereço pessoal do prestador.

## Comandos úteis

```bash
npm run build
./vendor/bin/pint --dirty
php artisan route:list
php artisan migrate
php artisan test
```

Com Docker:

```bash
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed
docker compose exec app php artisan route:list
docker compose exec app php artisan storage:link
```

O PostgreSQL cria o banco principal `propostei` e, na primeira inicialização do volume, também cria `propostei_test`.

## Deploy no Render

O `Dockerfile` de produção usa Apache + PHP no mesmo container. O Apache aponta para `/var/www/html/public`, respeita a variável `PORT` do Render e usa `10000` como fallback.

No start do container, o script [docker/start.sh](docker/start.sh) executa:

```bash
php artisan migrate --force
php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Não use `migrate:fresh` em produção.

Variáveis esperadas no Render:

```env
APP_NAME=propostei
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://seu-app.onrender.com

APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=pt_BR
APP_FAKER_LOCALE=pt_BR

LOG_CHANNEL=stderr
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=host-do-postgres-render
DB_PORT=5432
DB_DATABASE=propostei
DB_USERNAME=propostei
DB_PASSWORD=senha

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

MAIL_MAILER=log
```

Para testar a imagem localmente:

```bash
docker build -t propostei-render .
docker run --rm -p 10000:10000 --env-file .env -e PORT=10000 propostei-render
```

A aplicação deve responder em:

```text
http://localhost:10000
```
