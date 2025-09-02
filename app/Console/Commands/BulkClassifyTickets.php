<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\ClassifyTicket;
use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BulkClassifyTickets extends Command
{
    protected $signature = 'tickets:bulk-classify
        {--count=1000 : Number of tickets to enqueue for classification}
        {--only-unclassified=true : Only enqueue tickets that have not been AI-classified yet}
        {--chunk=1000 : Chunk size to use when scanning tickets}';

    protected $description = 'Bulk enqueue ticket classification jobs';

    public function handle(): int
    {
        // Avoid query log memory blowups
        DB::connection()->disableQueryLog();

        $targetCount = max(0, (int) $this->option('count'));
        if ($targetCount === 0) {
            $this->info('No tickets requested. Nothing to do.');
            return self::SUCCESS;
        }

        $chunkSize = max(1, (int) $this->option('chunk'));
        $onlyUnclassified = (bool) $this->option('only-unclassified');

        $base = Ticket::query()->orderBy('id');

        if ($onlyUnclassified) {
            $base->where(function ($q) {
                $q->whereNull('ai_confidence')
                    ->orWhereNull('ai_explanation');
            });
        }

        $totalMatching = (clone $base)->count();
        $plannedTotal = min($targetCount, $totalMatching);
        if ($plannedTotal === 0) {
            $this->info('No matching tickets found.');
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($plannedTotal);
        $bar->start();

        $dispatched = 0;

        // Use chunkById with a lightweight selection to minimize memory and payload size.
        // We only pass the model (with id) to the Job; Laravel will re-retrieve it when the job runs.
        $base->select('id')
            ->chunkById($chunkSize, function ($tickets) use (&$dispatched, $plannedTotal, $bar) {
                foreach ($tickets as $ticket) {
                    ClassifyTicket::dispatch($ticket);
                    $dispatched++;
                    $bar->advance();
                    if ($dispatched >= $plannedTotal) {
                        return false;
                    }
                }
                return true;
            });

        $bar->finish();
        $this->newLine(2);
        $this->info("Enqueued {$dispatched} ticket classification job(s)");

        return self::SUCCESS;
    }
}
