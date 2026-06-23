<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    // Parameter tambahan untuk status & message
    public string $status;
    public string $message;

    /**
     * Constructor custom: menerima data, status, dan message.
     * Mengikuti pola obatResource dari product service.
     */
    public function __construct($resource, string $status = 'berhasil', string $message = '')
    {
        parent::__construct($resource);
        $this->status = $status;
        $this->message = $message;
    }

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'status'  => $this->status,
            'message' => $this->message,
            'data'    => $this->resource,
        ];
    }
}
