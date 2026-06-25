<?php

namespace App\Console\Commands;

use App\Models\AiUnansweredQuery;
use Illuminate\Console\Command;

class ReviewUnansweredQueries extends Command
{
    protected $signature = 'ai:review-unanswered {--limit=20 : Jumlah query yang ditampilkan}';
    protected $description = 'Menampilkan query yang tidak terjawab oleh AI Copilot, diurutkan berdasarkan frekuensi';

    public function handle()
    {
        $limit = (int) $this->option('limit');

        $queries = AiUnansweredQuery::selectRaw('query_text, COUNT(*) as count, MAX(created_at) as last_asked_at')
            ->groupBy('query_text')
            ->orderByDesc('count')
            ->orderByDesc('last_asked_at')
            ->limit($limit)
            ->get();

        if ($queries->isEmpty()) {
            $this->info('Tidak ada query yang tidak terjawab.');
            return 0;
        }

        $this->info("{$queries->count()} query unik tidak terjawab (menampilkan {$limit} terbanyak):");
        $this->newLine();

        $rows = [];
        foreach ($queries as $q) {
            $rows[] = [
                $q->query_text,
                $q->count,
                \Carbon\Carbon::parse($q->last_asked_at)->format('d/m/Y H:i'),
            ];
        }

        $this->table(['Query', 'Frekuensi', 'Terakhir Ditanyakan'], $rows);
        $this->newLine();
        $this->info('Pertimbangkan untuk menambahkan sinonim baru di config/ai_intents.php jika query ini seharusnya bisa dijawab.');

        return 0;
    }
}
