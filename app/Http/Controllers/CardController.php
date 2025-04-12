<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UseCases\Card\Register;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Responses\DefaultResponse;
use App\Integrations\Banking\Card\Find;

class CardController extends Controller
{
    /**
     * Exibe dados de um cartão
     *
     * POST api/users/{id}/card
     *
     * @return JsonResponse
     */
    public function show(string $userId): JsonResponse
    {
        //PA4
        //A camada de integração deveria estar encapsulada na camada de use case
        $response = (new Find($userId))->handle();

        return $this->response(
            new DefaultResponse($response['data'])
        );
    }

    /**
     * Ativa um cartão
     *
     * POST api/users/{id}/card
     *
     * @return JsonResponse
     */
    //PM- Poderia ter sido utilizado um Validador para validar o request
    public function register(string $userId, Request $request): JsonResponse
    {
        $response = (new Register($userId, $request->pin, $request->card_id))->handle();

        return $this->response(
            new DefaultResponse($response['data'])
        );
    }
}
