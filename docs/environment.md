## PHP Laravel (Lumen) Project

https://lumen.laravel.com/docs/7.x/testing

### Project Structure

The project structure is the same as the standard Lumen Project:
```
├── app/
│   ├── Console/  (shouldn't be necessary, but not forbidden)
│   ├── Events/
│   │   └── Event.php
│   ├── Exceptions/
│   │   └── Handler.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Controller.php
│   │   └── Middleware/
│   │       └── Authenticate.php
│   ├── Jobs/
│   │   └── Job.php
│   ├── Listeners/
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   └── EventServiceProvider.php
│   └── User.php
├── bootstrap/
│   └── app.php
├── database/
│   ├── factories/
│   │   └── ModelFactory.php
│   ├── migrations/
│   └── seeds/
│       └── DatabaseSeeder.php
├── public/       (shouldn't be necessary, but not forbidden)
├── resources/
│   └── views/
├── routes/
│   └── web.php*
├── storage/      (shouldn't be necessary)
└── tests/
    └── TestCase.php*
```

Files shown above not marked with `*` are already in the runner and may be optionally included for customization or omitted.

The database location for SQLIte is the default location, `database/database.sqlite`.