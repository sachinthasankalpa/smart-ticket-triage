<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Ticket;
use App\Repositories\Interfaces\TicketRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class EloquentTicketRepository implements TicketRepositoryInterface
{
    public function getAll(array $filters): LengthAwarePaginator
    {
        $query = Ticket::query()->orderBy('created_at', 'desc');

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('subject', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('body', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->paginate();
    }

    public function findById(string $id): ?Ticket
    {
        return Ticket::find($id);
    }

    public function create(array $data): Ticket
    {
        return Ticket::create($data);
    }

    public function update(string $id, array $data): ?Ticket
    {
        $ticket = $this->findById($id);
        if ($ticket) {
            $ticket->update($data);
            return $ticket;
        }
        return null;
    }

    public function getStats(): array
    {
        // Better to have a fallback if view not present (e.g., before migration), going without it for now.
        $row = DB::selectOne('SELECT status_counts, category_counts, total_tickets FROM ticket_stats');

        return [
            'status_counts' => json_decode($row->status_counts ?? '{}', true) ?: [],
            'category_counts' => json_decode($row->category_counts ?? '{}', true) ?: [],
            'total_tickets' => (int)($row->total_tickets ?? 0),
        ];
    }

}
