<?php

namespace App\Jobs;

use App\Models\Obat;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
        Log::info("ProductService: Memproses update stok untuk Product ID {$this->productId}, Action: {$this->action}, Qty: {$this->quantity}");

        $obat = Obat::find($this->productId);

        if ($obat) {
            if ($this->action === 'subtract') {
                $obat->stock = $obat->stock - $this->quantity;
            } else {
                $obat->stock = $obat->stock + $this->quantity;
            }
            $obat->save();
            
            Log::info("ProductService: Stok berhasil diupdate. Stok baru: {$obat->stock}");
        } else {
            Log::error("ProductService: Product ID {$this->productId} tidak ditemukan saat update stok.");
        }
    }
}
