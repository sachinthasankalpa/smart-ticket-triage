<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Ideally, we would check if the index already exists before adding it
        if (Schema::hasTable('tickets')) {
            DB::statement('ALTER TABLE tickets ADD INDEX tickets_status_idx (status)');
            DB::statement('ALTER TABLE tickets ADD INDEX tickets_category_idx (category)');
        }

        DB::statement("CREATE OR REPLACE VIEW ticket_stats AS
            SELECT
              (
                SELECT JSON_OBJECTAGG(COALESCE(CAST(s.status AS CHAR), ''), s.cnt)
                FROM (
                  SELECT status, COUNT(*) AS cnt
                  FROM tickets
                  GROUP BY status
                ) AS s
              ) AS status_counts,
              (
                SELECT JSON_OBJECTAGG(COALESCE(CAST(c.category AS CHAR), ''), c.cnt)
                FROM (
                  SELECT category, COUNT(*) AS cnt
                  FROM tickets
                  GROUP BY category
                ) AS c
              ) AS category_counts,
              (SELECT COUNT(*) FROM tickets) AS total_tickets"
        );
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS ticket_stats');

        DB::statement('ALTER TABLE tickets DROP INDEX tickets_status_idx');
        DB::statement('ALTER TABLE tickets DROP INDEX tickets_category_idx');
    }
};
