<?php

declare(strict_types=1);

namespace App\Enums;

enum TicketCategory: string
{
    case TECHNICAL_SUPPORT = 'technical_support';
    case BILLING = 'billing';
    case SALES = 'sales';
    case GENERAL_INQUIRY = 'general_inquiry';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
