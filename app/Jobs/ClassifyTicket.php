<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Ticket;
use App\Services\TicketClassifier;

class ClassifyTicket implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Ticket $ticket)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(TicketClassifier $classifier): void
    {
        $classification = $classifier->classify($this->ticket->subject, $this->ticket->body);

        $updateData = [
            'ai_explanation' => $classification['explanation'],
            'ai_confidence' => $classification['confidence'],
        ];

        if (!$this->ticket->is_category_manual_override) {
            $updateData['category'] = $classification['category'];
        }

        $this->ticket->update($updateData);
    }
}
