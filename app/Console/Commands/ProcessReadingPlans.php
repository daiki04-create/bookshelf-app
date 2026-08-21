<?php

namespace App\Console\Commands;

use App\Models\ReadingPlan;
use App\Notifications\ReadingPlanReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessReadingPlans extends Command
{
    protected $signature = 'reading-plans:process';

    protected $description = '期限切れ読書計画の自動失効処理およびリマインダー通知の送信';

    public function handle(): int
    {
        $today = Carbon::today();

        ReadingPlan::where('status', 'in_progress')
            ->where('target_date', '<', $today)
            ->update(['status' => 'expired']);

        $plansToNotify = ReadingPlan::where('status', 'in_progress')
            ->whereIn('target_date', [$today, $today->copy()->addDays(3)])
            ->with(['user', 'book'])
            ->get();

        foreach ($plansToNotify as $plan) {
            $plan->user->notify(new ReadingPlanReminderNotification($plan));
        }

        $this->info('読書計画のバッチ処理が完了しました。');

        return Command::SUCCESS;
    }
}
