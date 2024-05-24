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
            'emp_no' => 'required',
            'emp_name' => 'required',
            'emp_role' => 'required',
            'name' => 'required|string|unique:users,name',
            'password' => [
                'required',
                'confirmed',
                Password::min(4)
            ],
            'emp_status' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'emp_no.required' => 'กรุณากรอกหมายเลขพนักงาน',
            'emp_name.required' => 'กรุณากรอกชื่อพนักงาน',
            'emp_role.required' => 'กรุณาเลือกบทบาทของพนักงาน',
            'name.required' => 'กรุณากรอกชื่อผู้ใช้',
            'name.unique' => 'ชื่อผู้ใช้นี้มีผู้ใช้งานแล้ว',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.confirmed' => 'รหัสผ่านที่ยืนยันไม่ตรงกัน',
            'password.min' => 'รหัสผ่านต้องมีอย่างน้อย 4 ตัวอักษร',
            'emp_status.required' => 'กรุณาเลือกสถานะของพนักงาน',
        ];
    }
}


