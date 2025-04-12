<?php

namespace Tests\Feature\Company;

use Tests\TestCase;
use App\Models\User;

class ShowTest extends TestCase
{
    //PM Faltam teste de falha para companhia não encontrada
    /**
     * Teste de busca de dados de empresa
     *
     * @return void
     */
    public function testShow()
    {

        //PM6
        $user    = User::factory()->user()->create();
        $company = $user->company;
        $token   = $user->createToken(config('auth.token_name'))->plainTextToken;

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];

        $response = $this->get('/api/company', $headers);

        $response->assertStatus(200);
        $response->assertJson(
            [
                'success' => true,
                'method'  => 'GET',
                'code'    => 200,
                'data'    => [
                    'id'   => $company->id,
                    'name' => $company->name,
                ],
            ],
            true
        );
    }
}
