<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Resources\V1\StatsResource;
use App\Services\TicketService;
use Illuminate\Http\Request;
use App\Http\Resources\V1\TicketResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function __construct(protected TicketService $ticketService)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $tickets = $this->ticketService->getAllTickets($request->only('search'));
        return TicketResource::collection($tickets);
    }

    public function store(StoreTicketRequest $request): JsonResponse
    {
        $ticket = $this->ticketService->createTicket($request->validated());
        return (new TicketResource($ticket))->response()->setStatusCode(201);
    }

    public function show(Ticket $ticket): TicketResource
    {
        return new TicketResource($ticket);
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket): TicketResource
    {
        $updatedTicket = $this->ticketService->updateTicket($ticket->id, $request->validated());
        return new TicketResource($updatedTicket);
    }

    public function destroy(Ticket $ticket): JsonResponse
    {
        abort(403, 'Method not allowed.');
    }

    public function classify(Ticket $ticket): JsonResponse
    {
        $this->ticketService->classifyTicket($ticket->id);
        return response()->json(['message' => 'Classification job dispatched.']);
    }

    public function stats(): StatsResource
    {
        $stats = $this->ticketService->getDashboardStats();
        return new StatsResource($stats);
    }
}
