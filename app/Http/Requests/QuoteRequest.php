<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class QuoteRequest extends FormRequest
{
    /**
     * Only logged-in users reach this request (the routes use the "auth" middleware).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'min:3', 'max:155'],
            'poet' => ['nullable', 'string', 'max:60'],
            'era_id' => ['required', 'integer', 'exists:eras,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'content.required' => 'اكتب البيت أولًا.',
            'content.string' => 'يجب أن يكون البيت نصًا.',
            'content.min' => 'يجب ألا يقل البيت عن 3 أحرف.',
            'content.max' => 'يجب ألا يزيد البيت عن 155 حرفًا.',
            'poet.string' => 'يجب أن يكون اسم الشاعر نصًا.',
            'poet.max' => 'يجب ألا يزيد اسم الشاعر عن 60 حرفًا.',
            'era_id.required' => 'اختر عصر البيت.',
            'era_id.integer' => 'اختر عصرًا صحيحًا.',
            'era_id.exists' => 'اختر عصرًا صحيحًا.',
        ];
    }
}
