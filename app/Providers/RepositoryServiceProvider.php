<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\TicketRepositoryInterface;
use App\Repositories\Eloquent\EloquentTicketRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TicketRepositoryInterface::class, EloquentTicketRepository::class);
    }
}
