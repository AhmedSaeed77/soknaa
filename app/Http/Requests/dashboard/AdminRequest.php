<?php

namespace App\Http\Requests\dashboard;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
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
        if ($this->isMethod('post'))
        {
            return [
                        'name' => 'required',
                        'email' => 'required|email|unique:admins,email',
                        'password' => 'required|confirmed',
                        'phone' => 'required',
                        'role' => 'required',
                    ];
        }
        if ($this->isMethod('put'))
        {
            $rules = [
                        'name' => 'required',
                        'email' => 'required|email|unique:admins,email,' . $this->id,
                        'password' => 'nullable',
                        'phone' => 'required',
                        'role' => 'required',
                    ];
    
            if ($this->isMethod('put') && $this->filled('password'))
            {
                $rules['password'] .= '|confirmed';
            }
    
            return $rules;
        }
        
    }
}
