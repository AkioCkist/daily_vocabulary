<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\EmailDigestService;

class ProcessEmailDigests extends Command
{
    protected $signature = 'emails:process-digests';

    protected $description = 'Process scheduled weekly/monthly email digests for users based on their subscription preferences.';

    public function handle(EmailDigestService $service): int
    {
        $this->info('Processing email digests...');

        $subs = Subscription::query()
            ->with('user')
            ->get();

        foreach ($subs as $sub) {
            if (!$sub->user) { continue; }

            // Incorrect words digest
            if ($sub->incorrect_words_frequency !== 'none') {
                $shouldSend = $service->shouldSend($sub, 'incorrect_words');
                if ($shouldSend) {
                    $service->sendIncorrectWordsReport($sub->user);
                }
            }

            // Topic summary digest
            if ($sub->topic_summary_frequency !== 'none') {
                $shouldSend = $service->shouldSend($sub, 'topic_summary');
                if ($shouldSend) {
                    $service->sendTopicSummary($sub->user);
                }
            }

            // Ads (ad-hoc; not scheduled here)
        }

        $this->info('Done.');
        return Command::SUCCESS;
    }
}
