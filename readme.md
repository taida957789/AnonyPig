## AnonyPig

Facebook anonymous publisher


### Installation

Install this repo
```bash
  git clone https://github.com/taida957789/AnonyPig.git
  cd AnonyPig
  composer install
```

Install redis-server and configure it

### Configuration

Copy file .env.example to  .env 

Enter command to generate laravel key

```bash
  php artisan key:generate
```
Setting your environment file ( .env )

```
APP_KEY=SomeRandomString

FACEBOOK_APP_ID=APPID
FACEBOOK_APP_SECRET=SECRET
RECAPTCHA_PUBLIC_KEY=PUBLISH_KEY
RECAPTCHA_PRIVATE_KEY=PRIVATE_KEY

DB_CONNECTION=sqlite
DB_HOST=localhost
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```

### Run
Listen laravel queue
```bash
  php artisan queue:listen
```
Start Dev Server
```bash
  php artisan serv
```
Start Production Mode Server  
```
  Use supervisor  ~  
  See at http://laravel.com/docs/5.1/queues
```





