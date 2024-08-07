<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules() : array
    {
        return [
            'email' => 'required|string',
            'password' => 'required',
            'remember' => 'boolean'
        ];
    }

    public function messages() : array{
        return [
            'email.required' => 'กรุณากรอกชื่อผู้ใช้งานระบบ',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
        ];
    }
}
