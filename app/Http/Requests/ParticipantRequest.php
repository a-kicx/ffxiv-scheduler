<?php

namespace App\Http\Requests;

use App\Enums\JobMode;
use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;

class ParticipantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Event $event */
        $event = $this->route('event');

        $rules = [
            'name' => ['required', 'string', 'max:100'],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'attendance' => ['required', 'array'],
            'attendance.*' => ['required', 'in:attend,maybe,decline'],
            'notes' => ['nullable', 'array'],
            'notes.*' => ['nullable', 'string', 'max:255'],
        ];

        if ($event && $event->job_mode !== JobMode::None) {
            if ($event->job_mode === JobMode::Single) {
                $rules['selected_jobs'] = ['nullable', 'integer', 'exists:ff14_jobs,id'];
            } else {
                $rules['selected_jobs'] = ['nullable', 'array'];
                $rules['selected_jobs.*'] = ['integer', 'exists:ff14_jobs,id'];
            }
        }

        if ($event && $event->sub_job_mode !== JobMode::None) {
            if ($event->sub_job_mode === JobMode::Single) {
                $rules['selected_sub_jobs'] = ['nullable', 'integer', 'exists:support_jobs,id'];
            } else {
                $rules['selected_sub_jobs'] = ['nullable', 'array'];
                $rules['selected_sub_jobs.*'] = ['integer', 'exists:support_jobs,id'];
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => '名前を入力してください。',
            'attendance.required' => '参加可否を選択してください。',
            'attendance.*.required' => '全ての日程の参加可否を選択してください。',
            'attendance.*.in' => '参加可否の値が不正です。',
        ];
    }
}
