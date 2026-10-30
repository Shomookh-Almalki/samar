<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SignupRequest extends FormRequest
{
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
            // 2-50 letters only (English or Arabic), spaces allowed between words.
            'name' => ['required', 'string', 'min:2', 'max:50', 'regex:/^[\pL ]+$/u'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب.',
            'name.min' => 'يجب ألا يقل الاسم عن حرفين.',
            'name.max' => 'يجب ألا يزيد الاسم عن 50 حرفًا.',
            'name.regex' => 'يجب أن يحتوي الاسم على حروف فقط.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'اكتب بريدًا إلكترونيًا صحيحًا.',
            'email.unique' => 'هذا البريد مسجّل من قبل.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.min' => 'يجب ألا تقل كلمة المرور عن 8 أحرف.',
        ];
    }
}
