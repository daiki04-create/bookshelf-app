<?php

namespace App\Notifications;

use App\Models\ReadingPlan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReadingPlanReminderNotification extends Notification
{
    use Queueable;

    protected ReadingPlan $plan;

    public function __construct(ReadingPlan $plan)
    {
        $this->plan = $plan;
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'reading_plan_id' => $this->plan->id,
            'book_title' => $this->plan->book->title,
            'target_date' => $this->plan->target_date,
            'message' => "読書計画「{$this->plan->book->title}」の期日（{$this->plan->target_date}）が近づいています。",
        ];
    }
}
