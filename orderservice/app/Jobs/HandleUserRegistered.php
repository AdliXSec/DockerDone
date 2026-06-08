<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Support\Facades\Log;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Jobs\RabbitMQJob;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\RabbitMQQueue;
use PhpAmqpLib\Message\AMQPMessage;

class HandleUserRegistered extends RabbitMQJob implements JobContract
{
    /**
     * Create a new job instance.
     */
    public function __construct(
        Container $container,
        RabbitMQQueue $rabbitmq,
        AMQPMessage $message,
        string $connectionName,
        string $queue
    ) {
        parent::__construct($container, $rabbitmq, $message, $connectionName, $queue);
    }

    /**
     * Execute the job.
     */
    public function fire(): void
    {
        $payload = $this->payload();
        Log::info("OrderService: Menerima event user baru dari Python (Plain JSON)", $payload);

        // Jika event-nya adalah user.registered, simpan/sync ke DB lokal OrderService
        if (isset($payload['event']) && $payload['event'] === 'user.registered') {
            $userData = $payload['data'];
            
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => 'external_user', 
                ]
            );
            
            Log::info("OrderService: Berhasil sinkronisasi user: " . $userData['email']);
        }

        // Jangan lupa hapus pesan dari antrean setelah diproses
        $this->delete();
    }
    
    /**
     * Backward compatibility for Laravel worker.
     */
    public function handle(): void
    {
        $this->fire();
    }
}
