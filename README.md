# Simple PHP Framework - Шаблон приложения

[![Версия PHP](https://img.shields.io/badge/PHP-%3E%3D8.0-blue)](https://php.net)
[![Framework](https://img.shields.io/badge/framework-sedalit%2Fphp--framework-brightgreen)](https://github.com/sedalit/simple-mvc-framework)
[![Лицензия](https://img.shields.io/badge/license-MIT-green)](LICENSE)

Шаблон приложения для [Simple PHP Framework](https://github.com/sedalit/simple-mvc-framework).

## 🚀 Быстрый старт

### Создание нового проекта

```bash
composer create-project sedalit/php-framework-skeleton my-app
cd my-app
```

### Настройка окружения

```bash
# Скопируйте файл окружения
cp .env.example .env

# Отредактируйте .env
nano .env
```

Настройте базу данных и другие параметры:

```env
# База данных
DB_HOST="localhost"
DB_NAME="your_database"
DB_USERNAME="your_username"
DB_PASSWORD="your_password"
DB_CHARSET="utf8mb4"

# Приложение
APP_NAME="Мое приложение"
APP_URL="http://localhost:8000"
APP_DEBUG=true

# Почта (опционально)
MAIL_HOST="smtp.example.com"
MAIL_USERNAME="your@email.com"
MAIL_PASSWORD="your_password"
MAIL_PORT="465"
```

### Настройка веб-сервера

#### Сервер разработки (встроенный PHP)

```bash
php -S localhost:8000 -t public
```

Откройте `http://localhost:8000` в браузере!

#### Конфигурация Apache

Укажите DocumentRoot на директорию `public`:

```apache
<VirtualHost *:80>
    ServerName myapp.local
    DocumentRoot /path/to/my-app/public
    
    <Directory /path/to/my-app/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Конфигурация Nginx

```nginx
server {
    listen 80;
    server_name myapp.local;
    root /path/to/my-app/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 📁 Структура проекта

```
my-app/
├── app/
│   ├── Controllers/           # Контроллеры приложения
│   │   ├── BaseController.php
│   │   └── IndexController.php
│   ├── Models/               # Модели данных
│   ├── Views/                # Шаблоны представлений
│   │   ├── layouts/          # Шаблоны макетов
│   │   │   └── default.php
│   │   ├── includes/         # Переиспользуемые компоненты
│   │   ├── error.php         # Страница ошибки
│   │   └── index.php         # Главная страница
│   ├── Middlewares/          # Пользовательские middleware
│   └── Validation/
│       └── Rules/            # Пользовательские правила валидации
├── config/
│   ├── init.php              # Конфигурация приложения
│   ├── db.php                # Конфигурация базы данных
│   ├── mail.php              # Конфигурация почты
│   ├── routes.php            # Определение маршрутов
│   └── serviceProviders.php  # Сервис-провайдеры
├── public/                   # Публичная веб-директория
│   ├── index.php             # Точка входа приложения
│   ├── .htaccess             # Правила перезаписи Apache
│   └── assets/               # Статические ресурсы
│       ├── css/
│       ├── js/
│       └── images/
├── tmp/
│   └── cache/                # Кеш приложения
├── uploads/                  # Загруженные пользователями файлы
├── tests/                    # Тесты приложения
├── vendor/                   # Зависимости Composer
├── bin/
│   └── console               # CLI инструмент
├── .env                      # Переменные окружения (создайте из .env.example)
├── .env.example              # Шаблон окружения
├── .gitignore
├── composer.json
└── README.md
```

## 🎯 Разработка приложения

### 1. Определение маршрутов

Отредактируйте `config/routes.php`:

```php
<?php

use App\Controllers\IndexController;
use App\Controllers\PostController;
use PHPFramework\Middlewares\AuthMiddleware;

// Главная страница
$app->router()->get('/', [IndexController::class, 'index']);

// Статьи
$app->router()->get('/posts', [PostController::class, 'index']);
$app->router()->get('/posts/(?<id>\d+)', [PostController::class, 'show']);
$app->router()->post('/posts', [PostController::class, 'store'], [AuthMiddleware::class]);

// Админ-панель
$app->router()->group('/admin', [
    $app->router()->get('/dashboard', [AdminController::class, 'index']),
    $app->router()->get('/users', [AdminController::class, 'users']),
])->middleware([AuthMiddleware::class]);
```

### 2. Создание контроллера

```bash
php bin/console make:controller PostController
```

Это создаст `app/Controllers/PostController.php`:

```php
<?php

namespace App\Controllers;

class PostController extends BaseController 
{
    public function index(): mixed
    {
        $posts = db()->table('posts')
            ->where('status', 'published')
            ->orderBy('created_at', 'DESC')
            ->get();
        
        return $this->render('posts/index', [
            'title' => 'Все статьи',
            'posts' => $posts
        ]);
    }
    
    public function show(): mixed
    {
        $id = router()->routeParam('id');
        
        $post = db()->table('posts')
            ->where('id', $id)
            ->first();
        
        if (!$post) {
            abort('Статья не найдена', 404);
        }
        
        return $this->render('posts/show', ['post' => $post]);
    }
    
    public function store(): mixed
    {
        $data = request()->post();
        
        // Валидация
        if (!validate($data, [
            'title' => 'required|min:3|max:200',
            'content' => 'required|min:10'
        ])) {
            return response()->redirect('/posts/create')
                ->send();
        }
        
        // Сохранение
        $id = db()->table('posts')->insert([
            'title' => $data['title'],
            'content' => $data['content'],
            'user_id' => session()->get('user_id'),
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        session()->setFlash('success', 'Статья успешно создана!');
        return response()->redirect("/posts/{$id}")->send();
    }
}
```

### 3. Создание модели

```bash
php bin/console make:model Post
```

Это создаст `app/Models/Post.php`:

```php
<?php

namespace App\Models;

use PHPFramework\Model;

class Post extends Model 
{
    protected array $fillable = ['title', 'content', 'user_id', 'status'];
    
    protected function tableName(): string 
    {
        return 'posts';
    }
    
    protected function primaryKeyName(): string 
    {
        return 'id';
    }
}
```

### 4. Создание представлений

Создайте `app/Views/posts/index.php`:

```php
<div class="container mt-4">
    <h1><?= h($title) ?></h1>
    
    <?php if (empty($posts)): ?>
        <p>Статьи не найдены.</p>
    <?php else: ?>
        <div class="row">
            <?php foreach($posts as $post): ?>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="<?= baseUrl("/posts/{$post['id']}") ?>">
                                    <?= h($post['title']) ?>
                                </a>
                            </h5>
                            <p class="card-text">
                                <?= h(substr($post['content'], 0, 150)) ?>...
                            </p>
                            <small class="text-muted">
                                <?= date('d.m.Y', strtotime($post['created_at'])) ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
```

### 5. Создание пользовательского Middleware

```bash
php bin/console make:middleware AdminMiddleware
```

Отредактируйте `app/Middlewares/AdminMiddleware.php`:

```php
<?php

namespace App\Middlewares;

use PHPFramework\Interfaces\MiddlewareInterface;
use PHPFramework\Request;
use PHPFramework\Response;

class AdminMiddleware implements MiddlewareInterface 
{
    public function handle(Request $request, Response $response, callable $next): mixed
    {
        $user = session()->get('user');
        
        if (!$user || $user['role'] !== 'admin') {
            abort('Доступ запрещен', 403);
        }
        
        return $next();
    }
}
```

### 6. Создание пользовательского правила валидации

```bash
php bin/console make:rule PhoneRule
```

Отредактируйте `app/Validation/Rules/PhoneRule.php`:

```php
<?php

namespace App\Validation\Rules;

use PHPFramework\Validation\ValidationRule;

class PhoneRule extends ValidationRule 
{
    protected string $message = "Поле :fieldname: должно содержать валидный номер телефона";
    
    public static function key(): ?string 
    {
        return 'phone';
    }
    
    public function passes(): bool 
    {
        return preg_match('/^\+?[1-9]\d{10,14}$/', $this->value);
    }
}
```

Использование в валидации:

```php
validate($data, [
    'phone' => 'required|phone'
]);
```

## 🎨 CLI команды

```bash
# Генерация файлов
php bin/console make:controller UserController
php bin/console make:model User
php bin/console make:middleware RoleMiddleware
php bin/console make:rule CustomRule

# Управление приложением
php bin/console cache:clear       # Очистить кеш
php bin/console app:setup         # Создать структуру директорий

# Помощь
php bin/console help
```

## 🗄️ Настройка базы данных

### Создание таблиц

Пример таблицы `users`:

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

Пример таблицы `posts`:

```sql
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content TEXT NOT NULL,
    status ENUM('draft', 'published') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### Настройка белого списка таблиц

Отредактируйте `config/db.php`, чтобы определить разрешенные таблицы:

```php
const TABLES_WHITELIST = [
    'users',
    'posts',
    'comments',
    'categories'
];
```

## 🔐 Пример аутентификации

### Контроллер входа

```php
public function login(): mixed
{
    if (request()->isPost()) {
        $data = request()->post();
        
        if (!validate($data, [
            'email' => 'required|email',
            'password' => 'required'
        ])) {
            return $this->render('auth/login', [
                'errors' => validator()->errors()
            ]);
        }
        
        $user = db()->table('users')
            ->where('email', $data['email'])
            ->first();
        
        if ($user && password_verify($data['password'], $user['password'])) {
            session()->set('user', $user);
            return response()->redirect('/dashboard')->send();
        }
        
        return $this->render('auth/login', [
            'error' => 'Неверные учетные данные'
        ]);
    }
    
    return $this->render('auth/login');
}

public function logout(): mixed
{
    session()->forget('user');
    return response()->redirect('/')->send();
}
```

## 👤 Автор

**Владислав Агарков**
- GitHub: [@sedalit](https://github.com/sedalit)
- Email: vlad.agarkov@alto-ai.ru