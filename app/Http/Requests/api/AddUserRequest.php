<?php

namespace App\Http\Requests\api;

use Illuminate\Foundation\Http\FormRequest;

class AddUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
                    'name' => 'required',
                    'parent_phone' => 'required',
                    'phone' => 'required',
                    'type' => 'required',
                    'sex' => 'required',

                    'age' => 'required',
                    'child_num' => 'required',
                    'typemerrage' => 'required',
                    'familysitiation' => 'required',

                    'country' => 'required',
                    'nationality' => 'required',
                    'city' => 'required',
                    'religion' => 'required',

                    'weight' => 'required',
                    'length' => 'required',
                    'skin_colour' => 'required',
                    'physique' => 'required',
                    'health_statuse' => 'required',
                    // 'beard' => 'required',
                    'prayer' => 'required',
                    'smoking' => 'required',
                    'educational_level' => 'required',
                    'financial_statuse' => 'required',
                    'employment' => 'required',
                    'job' => 'required',
                    'monthly_income' => 'required',
                    'life_partner_info' => 'required',
                    'my_information' => 'required',
                    'images' => 'required_if:type,ذكر,أنثى|array',
                    'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                ];
    }
}
