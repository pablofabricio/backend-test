<?php

namespace Tests\Feature\Account;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use Illuminate\Support\Facades\Http;
use Tests\Providers\Banking\AccountProvider;
use Symfony\Component\HttpFoundation\Response;

class ActiveTest extends TestCase
{
    /**
     * Teste de desbloqueio de conta
     *
     * @return void
     */

    /**
     * Sugestão:
     * A rota '/api/users/$user->id/account/active' está hardcoded, o que pode gerar um erro caso haja alguma alteração futura
     * Não há teste para falhas ou comportamentos inesperados
     * ex: falha de autenticação, conta não registrada e usuário sem permissão para ativar a conta
     * Nomes mais intuitivos para os testes
     */

    public function testActive()
    {
        $user    = User::factory()->user()->create();
        $account = Account::factory()->registered()->create(
            [
                'user_id' => $user->id,
            ]
        );
        $token = $user->createToken(config('auth.token_name'))->plainTextToken;

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];

        $bankingResponse = app(AccountProvider::class)->active();

        $urlAuth   = config('auth.banking_base_url') . 'auth/vexpenses/token';
        $urlActive = config('auth.banking_base_url') . "accounts/$account->external_id/status/active";

        Http::fake(
            [
                $urlAuth      => Http::response(
                    [],
                    Response::HTTP_OK
                ),
                $urlActive  => Http::response(
                    $bankingResponse,
                    Response::HTTP_OK,
                ),
                '*'           => Http::response(
                    [],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                ),
            ]
        );

        $response = $this->put("/api/users/$user->id/account/active", [], $headers);

        Http::assertSentInOrder(
            [
                $urlAuth,
                $urlActive,
            ]
        );

        $response->assertStatus(200);
        $response->assertJson(
            [
                'success' => true,
                'method'  => 'PUT',
                'code'    => 200,
                'data'    => null,
            ],
            true
        );
    }
}
