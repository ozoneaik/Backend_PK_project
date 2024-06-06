<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QcProductRequest extends FormRequest
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
            'pid' => 'required|unique:qc_prods,pid',
            'le_id' => 'required',
            'pname' => 'required',
            'timeperpcs' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'pid.unique' => 'มีรหัสสินค้านี้ในฐานข้อมูลอยู่แล้ว',
            'pid.required' => 'กรุณากรอกรหัสสินค้า',
            'le_id.required' => 'กรุณากรอกระดับความยาก',
            'pname.required' => 'กรุณากรอกชื่อสินค้า',
            'timeperpcs.required' => 'กรุณากรอกระยะเวลามาตรฐาน (HH:MM:SS)',
        ];
    }
}
