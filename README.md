# 🚀 WASF Core

**The Core Engine of the WASF PHP Framework**

WASF Core adalah inti dari seluruh ekosistem WASF Framework.  
Package ini menyediakan semua komponen fundamental seperti Routing, HTTP Kernel, Console Engine, View/Blade compiler, Database Wrapper, Environment loader, dan berbagai helper yang digunakan oleh project `wasf-app`.

---

## ✨ Apa itu WASF Core?

WASF Core *bukan aplikasi*, tetapi *mesin inti (engine)* yang menjalankan seluruh kemampuan framework WASF.  
Developer biasanya tidak membuat aplikasi langsung di atas Core — melainkan menggunakan template `wasf-app`.

Namun untuk kontribusi, debugging, atau pengembangan fitur baru, inilah package yang memuat seluruh sistem:

- Routing system  
- Request/Response kernel  
- Exception handler  
- Environment loader  
- HMVC module loader  
- Blade template compiler  
- CLI command handler  
- Database PDO wrapper  

---

# 📦 Instalasi

Tambahkan ke project berbasis Composer:

```bash
composer require abesarrr/wasf-core
```

Jika digunakan bersama `wasf-app`, package ini sudah otomatis terinstal.

---

# 🧬 Fitur Inti WASF Core

## ✔ 1. Routing Engine

Routing di WASF dibangun sederhana namun fleksibel:

```php
$router->get('/', 'HomeController@index');
$router->get('/user/{id}', 'UserController@show');
$router->post('/login', 'AuthController@login');
```

Mendukung:

- Method GET, POST, PUT, PATCH, DELETE  
- Parameter dinamis `{id}`  
- Controller@method format  
- Auto-binding module routes  
- (Roadmap) Middleware & route group  

---

## ✔ 2. HTTP Kernel

HTTP Kernel menangani:

- Mem-parsing request  
- Menentukan route yang cocok  
- Menjalankan controller  
- Menghasilkan response yang benar  
- Handling status code  

Contoh response:

```php
return response()->json(['success' => true]);
```

---

## ✔ 3. Console Kernel

Semua perintah CLI WASF dijalankan melalui console core:

```bash
php wasf list
php wasf make:controller HomeController
php wasf make:module Blog
php wasf migrate
php wasf key:generate
```

Semua command berada di:

```
src/Console/Commands/
```

Developer bisa membuat command custom.

---

## ✔ 4. Blade Template Compiler

Core menyediakan compiler untuk Blade engine:

```php
return view('dashboard', ['title' => 'Welcome']);
```

View akan dikompilasi ke:

```
storage/views/
```

---

## ✔ 5. HMVC Module Loader

WASF Core mendukung arsitektur modul:

```
Modules/Blog/
 ├─ Controllers/
 ├─ Models/
 ├─ Views/
 └─ routes.php
```

Semua route module akan otomatis dimuat oleh Core.

---

## ✔ 6. Database PDO Wrapper

Menggunakan PDO secara sederhana:

```php
DB::connect([
    'driver'   => 'mysql',
    'host'     => '127.0.0.1',
    'port'     => 3306,
    'database' => 'wasf',
    'username' => 'root',
    'password' => '',
]);
```

Ambil instance PDO:

```php
$pdo = DB::pdo();
```

---

## ✔ 7. Helper Functions

Core menyediakan helper umum:

```php
base_path(); 
app_path(); 
config();
env();
view();
response();
```

Termasuk autoloader untuk `.env` dan config.

---

## ✔ 8. Environment Loader (.env)

Mengambil env dengan:

```php
env('WASF_KEY');
```

Core juga menyediakan fitur:

- Memuat `.env` otomatis  
- Mendukung WASF_KEY sebagai encryption key  

---

# 📁 Struktur Direktori WASF Core

```txt
src/
 ├─ Console/
 │   ├─ Commands/
 │   └─ Kernel.php
 ├─ Database/
 │   └─ DB.php
 ├─ Exceptions/
 ├─ Http/
 │   ├─ Controllers/
 │   ├─ Request.php
 │   ├─ Response.php
 │   └─ Router.php
 ├─ Support/
 │   ├─ helpers.php
 │   ├─ Env.php
 │   └─ View.php
 └─ Wasf.php
```

---

# 🔧 Integrasi Dengan WASF App

`wasf-app` menjalankan bootstrap core:

```
bootstrap/app.php
```

Yang akan:

1. Load environment  
2. Load core engine  
3. Register router  
4. Load module routes  
5. Run the application  

---

# 🧵 Kontribusi

Karena Core adalah engine utama, kontribusi harus mengikuti standar stabilitas yang tinggi.

1. Fork repository  
2. Buat branch baru:  
   ```bash
   git checkout -b feature/nama-fitur
   ```
3. Tambahkan dokumentasi & test (jika tersedia)  
4. Submit pull request  

---

# 🛡 Keamanan

Untuk pelaporan celah keamanan:

📧 **wasuryanto3@gmail.com**  
`subject: "WASF Core Security"`

Jangan membuat issue publik.

---

# 🗺 Roadmap WASF Core

- [ ] Middleware Support  
- [ ] Session Manager  
- [ ] Cookie Encryption  
- [ ] Cache System  
- [ ] Validation Engine  
- [ ] Module Autodiscovery  
- [ ] File Storage Abstraction  
- [ ] Logging System  
- [ ] HTTP Client  
- [ ] Event & Listener System  

---

# 📄 Lisensi

WASF Core dirilis dengan lisensi **MIT**.

---

# 🧵 Repository

https://github.com/abesarrr/wasf-core
