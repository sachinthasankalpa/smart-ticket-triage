<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'total_tickets' => $this->resource['total_tickets'],
            'status_counts' => $this->resource['status_counts'],
            'category_counts' => $this->resource['category_counts'],
        ];
    }
}
