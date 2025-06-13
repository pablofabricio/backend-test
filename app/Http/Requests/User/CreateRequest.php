<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
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
            'document_number' => 'required|regex:/[0-9]{11}/i',
            'name'            => 'required',
            'email'           => 'required|email',
            'password'        => 'required',
            'type'            => 'required|in:USER,VIRTUAL,MANAGER'
        ];
    }
}
