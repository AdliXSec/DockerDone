<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateProductStock implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $productId;
    public $quantity;
    public $action; // 'subtract' or 'add'

    /**
     * Create a new job instance.
     */
    public function __construct($productId, $quantity, $action = 'subtract')
    {
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->action = $action;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // This handle will be empty in OrderService 
        // because it's only meant to be dispatched to ProductService
    }
}
