<?php

namespace App\Http\Requests\api;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
                    'email' => 'required|email',
                    'password' => 'required|confirmed',
                    'nickname' => 'required|regex:/^[^\s]+$/',
                    'phone' => 'required',
                    'type' => 'required',
                    'sex' => 'required',

                    // 'age' => 'required',
                    'age' => 'required_if:type,زوج,زوجه',
                    'child_num' => 'required_if:type,زوج,زوجه',
                    'typemerrage' => 'required_if:type,زوج,زوجه',
                    'familysitiation' => 'required_if:type,زوج,زوجه',

                    'country' => 'required_if:type,زوج,زوجه',
                    'nationality' => 'required_if:type,زوج,زوجه',
                    'city' => 'required_if:type,زوج,زوجه',
                    'religion' => 'required_if:type,زوج,زوجه',

                    'weight' => 'required_if:type,زوج,زوجه',
                    'length' => 'required_if:type,زوج,زوجه',
                    'skin_colour' => 'required_if:type,زوج,زوجه',
                    'physique' => 'required_if:type,زوج,زوجه',
                    'health_statuse' => 'required_if:type,زوج,زوجه',
                    // 'beard' => 'required_if:type,زوج,زوجه',
                    'prayer' => 'required_if:type,زوج,زوجه',
                    'smoking' => 'required_if:type,زوج,زوجه',
                    'educational_level' => 'required_if:type,زوج,زوجه',
                    'financial_statuse' => 'required_if:type,زوج,زوجه',
                    'employment' => 'required_if:type,زوج,زوجه',
                    'job' => 'required_if:type,زوج,زوجه',
                    'monthly_income' => 'required_if:type,زوج,زوجه',
                    'life_partner_info' => 'required_if:type,زوج,زوجه',
                    'my_information' => 'required_if:type,زوج,زوجه',
                    'images' => 'required_if:type,ذكر,أنثى|array',
                    'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                ];
    }
}
