<?php

declare(strict_types=1);

namespace App\Services;

use App\Jobs\ClassifyTicket;
use App\Models\Ticket;
use App\Repositories\Interfaces\TicketRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class TicketService
{
    public function __construct(protected TicketRepositoryInterface $ticketRepository)
    {
    }

    public function getAllTickets(array $filters): LengthAwarePaginator
    {
        return $this->ticketRepository->getAll($filters);
    }

    public function getTicketById(string $id): ?Ticket
    {
        return $this->ticketRepository->findById($id);
    }

    public function createTicket(array $data): Ticket
    {
        return $this->ticketRepository->create($data);
    }

    public function updateTicket(string $id, array $data): ?Ticket
    {
        if (isset($data['category'])) {
            $data['is_category_manual_override'] = true;
        }
        return $this->ticketRepository->update($id, $data);
    }

    public function classifyTicket(string $id): ?Ticket
    {
        $ticket = $this->ticketRepository->findById($id);
        if ($ticket) {
            ClassifyTicket::dispatch($ticket);
        }
        return $ticket;
    }

    public function getDashboardStats(): array
    {
        return $this->ticketRepository->getStats();
    }
}
