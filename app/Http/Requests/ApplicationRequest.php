<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    // public function authorize()
    // {
    //     return true;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "name" => "required|string|min:3|max:150",
            "surname" => "required|string|min:3|max:150",
            "lastname" => "required|string|min:3|max:150",
            "birth_date" => "required|string|min:3|max:15",
            "phone_number" => "required|string|min:8|max:11",
            "email" => "required|string|min:8|max:120",
            "rate_hour" => "required|integer",
            "iswoman" => "required|boolean",
            "avatar" => "required",

            
        ];
    }
}