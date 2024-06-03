<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class SignupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:4',
            'emp_role' => 'required|string',
            'authcode' => 'required|string',
            'emp_status' => 'required|string',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.required' => 'จำเป็นต้องระบุอีเมล',
            'email.email' => 'อีเมลต้องเป็นที่อยู่อีเมลที่ถูกต้อง',
            'email.unique' => 'อีเมลนี้มีผู้ใช้แล้ว',
            'name.required' => 'จำเป็นต้องระบุชื่อ',
            'password.required' => 'จำเป็นต้องระบุรหัสผ่าน',
            'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 4 ตัวอักษร',
            'emp_role.required' => 'จำเป็นต้องระบุตำแหน่งงาน',
            'authcode.required' => 'จำเป็นต้องระบุรหัสยืนยัน',
            'emp_status.required' => 'จำเป็นต้องระบุสถานะพนักงาน',
        ];
    }
}
