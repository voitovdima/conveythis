<?php

namespace App\Services;

use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class FileService
{
    protected $connection;
    protected $channel;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST', '127.0.0.1'),
            env('RABBITMQ_PORT', 5672),
            env('RABBITMQ_USER', 'guest'),
            env('RABBITMQ_PASSWORD', 'guest'),
            env('RABBITMQ_VHOST', '/')
        );
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare(env('RABBITMQ_QUEUE', 'file_notifications'), false, true, false, false);
    }

    public function store(UploadedFile $file): File
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('uploads', $fileName, 'public');

        return File::create([
            'name' => $file->getClientOriginalName(),
            'path' => $filePath,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'expires_at' => now()->addHours(24), // Automatic deletion after 24 hours
        ]);
    }

    public function delete(int $id, bool $isManual = false): void
    {
        $file = File::findOrFail($id);
        Storage::disk('public')->delete($file->path);
        $fileName = $file->name;
        $file->delete();

        // Sending a message to RabbitMQ
        $this->sendNotification($fileName, $isManual);
    }

    protected function sendNotification(string $fileName, bool $isManual): void
    {
        $messageBody = json_encode([
            'email' => env('EMAIL_TO'),
            'name' => $fileName,
            'deleted_at' => now()->toDateTimeString(),
            'type' => $isManual ? 'manual' : 'auto',
        ]);

        $message = new AMQPMessage($messageBody, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
        $this->channel->basic_publish($message, '', env('RABBITMQ_QUEUE', 'file_notifications'));
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
