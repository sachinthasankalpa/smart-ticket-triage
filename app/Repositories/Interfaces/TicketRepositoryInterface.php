<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Ticket;
use Illuminate\Pagination\LengthAwarePaginator;

interface TicketRepositoryInterface
{
    public function getAll(array $filters): LengthAwarePaginator;
    public function findById(string $id): ?Ticket;
    public function create(array $data): Ticket;
    public function update(string $id, array $data): ?Ticket;
    public function getStats(): array;
}
