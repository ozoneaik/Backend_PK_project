<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property mixed $timeperpcs
 * @property mixed $pid
 * @property mixed $pname
 * @property mixed $levelid
 */
class QcProductRequest extends FormRequest
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
            'pid' => 'required|unique:qc_prods,pid',
            'levelid' => 'required',
            'pname' => 'required',
            'timeperpcs' => 'required',
        ];
    }

    public function messages() : array
    {
        return [
            'pid.unique' => 'มีรหัสสินค้านี้ในฐานข้อมูลอยู่แล้ว',
            'pid.required' => 'กรุณากรอกรหัสสินค้า',
            'levelid.required' => 'กรุณากรอกระดับความยาก',
            'pname.required' => 'กรุณากรอกชื่อสินค้า',
            'timeperpcs.required' => 'กรุณากรอกระยะเวลามาตรฐาน (HH:MM:SS)',
        ];
    }
}
