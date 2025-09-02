<?php

declare(strict_types=1);

use App\Enums\TicketCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\TicketStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('subject');
            $table->text('body');
            $table->enum('status', TicketStatus::values())->default(TicketStatus::OPEN->value);
            $table->enum('category', TicketCategory::values())->nullable();
            $table->text('ai_explanation')->nullable();
            $table->decimal('ai_confidence', 3)->nullable();
            $table->text('internal_note')->nullable();
            $table->boolean('is_category_manual_override')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
