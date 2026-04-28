<?php

namespace App\Http\Requests;

use App\Enums\AttendanceMode;
use App\Enums\JobMode;
use App\Enums\PartyType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'party_type' => ['required', Rule::enum(PartyType::class)],
            'job_mode' => ['required', Rule::enum(JobMode::class)],
            'sub_job_mode' => ['required', Rule::enum(JobMode::class)],
            'attendance_mode' => ['required', Rule::enum(AttendanceMode::class)],
            'role_config' => ['required', 'array'],
            'role_config.units' => ['required', 'array', 'min:1'],
            'role_config.units.*.label' => ['required', 'string', 'max:30'],
            'role_config.units.*.tank' => ['required', 'integer', 'min:0', 'max:8'],
            'role_config.units.*.healer' => ['required', 'integer', 'min:0', 'max:8'],
            'role_config.units.*.dps' => ['required', 'integer', 'min:0', 'max:8'],
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'gte:date_from'],
            'times' => ['nullable', 'array'],
            'times.*' => ['string', 'regex:/^\d{2}:\d{2}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'イベント名を入力してください。',
            'date_from.required' => '開始日を入力してください。',
            'date_to.required' => '終了日を入力してください。',
            'date_to.gte' => '終了日は開始日以降にしてください。',
        ];
    }
}
