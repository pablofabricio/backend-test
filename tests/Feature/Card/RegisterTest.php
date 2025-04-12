<?php

namespace Tests\Feature\Card;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use Illuminate\Support\Facades\Http;
use Tests\Providers\Banking\CardProvider;
use Symfony\Component\HttpFoundation\Response;

class RegisterTest extends TestCase
{
    //PM9
    //Faltam testes para os casos de erro na validacao de externalId e no findAccountId, seria interessante
    //também os testes de validação do request se tivesse sido utilizado um validador na função register.
    /**
     * Teste de registro de cartão
     *
     * @return void
     */
    public function testRegister()
    {
        $user    = User::factory()->user()->create();
        $account = Account::factory()->registered()->create(
            [
                'user_id' => $user->id,
            ]
        );
        //PM6
        $token = $user->createToken(config('auth.token_name'))->plainTextToken;

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];

        $bankingResponse = app(CardProvider::class)->cardDetails();

        $urlAuth      = config('auth.banking_base_url') . 'auth/vexpenses/token';
        $urlRegister  = config('auth.banking_base_url') . "account/$account->external_id/card";

        Http::fake(
            [
                $urlAuth      => Http::response(
                    [],
                    Response::HTTP_OK
                ),
                $urlRegister  => Http::response(
                    $bankingResponse,
                    Response::HTTP_OK,
                ),
                '*'           => Http::response(
                    [],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                ),
            ]
        );

        $body = [
            'pin'     => $this->faker->regexify('\d{4}'),
            'card_id' => $this->faker->uuid,
        ];
        //PM8
        $response = $this->postJson("/api/users/$user->id/card/register", $body, $headers);

        Http::assertSentInOrder(
            [
                $urlAuth,
                $urlRegister,
            ]
        );

        $response->assertStatus(200);
        $response->assertJson(
            [
                'success' => true,
                'method'  => 'POST',
                'code'    => 200,
                'data'    => $bankingResponse,
            ],
            true
        );

        $this->assertDatabaseHas(
            'accounts',
            [
                'user_id' => $user->id,
            ]
        );
    }
}
