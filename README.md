# File Upload Project

This project is a Laravel-based web application for asynchronous file uploads (PDF, DOCX) with automatic deletion after 24 hours and RabbitMQ notifications. It includes a file management page and uses Bootstrap for styling.

## Requirements

- **PHP**: 8.2 or higher
- **Composer**: 2.8.6 or higher
- **Node.js**: Optional, for frontend assets
- **MySQL/SQLite**: For the database
- **RabbitMQ**: For message queueing
- **Homebrew**: Recommended for macOS

## Installation

### 1. Clone the Repository

```
git clone https://github.com/voitovdima/conveythis.git
cd conveythis
```

### 2. On a server (add to Cron)

```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1

## Project Structure

- **app/Services/FileService.php**: Handles file storage and deletion logic.
- **app/Http/Controllers/FileUploadController.php**: Manages HTTP requests.
- **resources/views/upload.blade.php**: File upload page.
- **resources/views/files.blade.php**: File management page.
- **app/Console/Commands/DeleteExpiredFiles.php**: Command for deleting expired files.
