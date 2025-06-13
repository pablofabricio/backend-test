<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    /**
     * Sugestão:
     * Adicionar a validação min e max
     * O type deve conter os valores do enum já sugeridos anteriormente.
     * ex: 'type'=> [
     *          'required',
     *         Rule::in(array_column(UserType::cases(), 'value')),
     *    ],
     */
    public function rules(): array
    {
        return [
            'user_document_number'    => 'required|regex:/[0-9]{11}/i',
            'user_name'               => 'required',
            'company_document_number' => 'required|regex:/[0-9]{14}/i',
            'company_name'            => 'required',
            'email'                   => 'required|email',
            'password'                => 'required',
        ];
    }
}
