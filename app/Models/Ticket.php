<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TicketCategory;
use App\Enums\TicketStatus;
use Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    /** @use HasFactory<TicketFactory> */
    use HasFactory, HasUlids;

    protected $fillable = [
        'subject',
        'body',
        'status',
        'category',
        'ai_explanation',
        'ai_confidence',
        'internal_note',
        'is_category_manual_override'
    ];

    protected $casts = [
        'status' => TicketStatus::class,
        'category' => TicketCategory::class,
        'ai_confidence' => 'float',
        'is_category_manual_override' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
