Laravel FilePond Chunk Upload
A template project demonstrating how to implement chunk file uploads in a Laravel backend with FilePond in a React frontend.
Features

Chunked file uploads for improved performance with large files
Temporary storage of files during upload process
Easy integration with React using FilePond
Simple API endpoints for handling uploaded files

Installation

Clone the repository

```bash
cd chunk_upload
```

Install PHP dependencies
```bash
composer install
```

Set up environment file
```bash
cp .env.example .env
```

Generate application key

```bash
php artisan key:generate
```

Configure your database in the .env file

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

Run migrations
```bash
php artisan migrate
```

Create a symbolic link for storage

```bash
php artisan storage:link
```

Install NPM dependencies

```bash
npm install
```

Build assets
```bash
npm run dev
```

Usage
Start the development server
```bash
php artisan serve
```

Accessing uploaded files
Uploaded images are temporarily stored in:
storage/public/tmp/{folder_name}/{image_name}
You can access these images via URL:
http://127.0.0.1:8000/storage/tmp/{folder_name}/{image_name}

Configuration
You can configure the chunk size and other FilePond options in the React components.
Contributing
Contributions are welcome! Please feel free to submit a Pull Request.
License
This project is open-sourced software licensed under the MIT license.