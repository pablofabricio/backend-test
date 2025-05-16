<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    // sugestão: utilizar DatabaseMigrations para utilizar as migrações na execução
    // e implementar a ferramente coverage para cobrir toda a aplicação

    /**
     * Teste de login de usuário quando não autorizados
     *
     * @return void
     */
    public function testLoginWhenUnauthorized()
    {
        $user = User::factory()->create();

        $headers = [
            'Authorization' => 'Basic ' . base64_encode($user->email . ':wrong_password'),
        ];

        $response = $this->post('/api/users/login', [], $headers);

        $response->assertStatus(401);
        $response->assertJson(
            [
                'success' => false,
                'method'  => 'POST',
                'code'    => 401,
                'data'    => null,
            ],
            true
        );
        $response->assertJsonStructure(
            [
                'errors',
            ]
        );
    }

    /**
     * Teste de login de usuário com sucesso
     *
     * @return void
     */
    public function testLoginWithSuccess()
    {
        $user = User::factory()->create();

        $headers = [
            'Authorization' => 'Basic ' . base64_encode($user->email . ':password'),
        ];

        $response = $this->post('/api/users/login', [], $headers);

        $response->assertStatus(200);
        $response->assertJson(
            [
                'success' => true,
                'method'  => 'POST',
                'code'    => 200,
            ],
            true
        );
        $response->assertJsonStructure(
            [
                'data' => [
                    'access_token'
                ]
            ]
        );

    }
}
