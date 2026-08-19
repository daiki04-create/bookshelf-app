<?php

namespace App\Http\Requests\ReadingPlan;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReadingPlanRequest extends FormRequest
{
    /**
     * リクエストの実行が許可されているか判定
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーションルール
     */
    public function rules(): array
    {
        return [
            'target_date' => ['required', 'date', 'after_or_equal:today'],
        ];
    }

    /**
     * バリデーションエラーメッセージ
     */
    public function messages(): array
    {
        return [
            'target_date.required' => '読了予定日を入力してください。',
            'target_date.date' => '有効な日付形式で入力してください。',
            'target_date.after_or_equal' => '読了予定日は本日以降の日付を指定してください。',
        ];
    }
}
