<?php

namespace App\Http\Controllers;

use App\UseCases\Account\Show;
use App\UseCases\Account\Block;
use App\UseCases\Account\Active;
use Illuminate\Http\JsonResponse;
use App\UseCases\Account\Register;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Responses\DefaultResponse;
use App\Http\Resources\Account\ShowResource;

/*

AJUSTES: 

6. Utilizar padronização nos status code, tudo está retornando como 200 caso sucesso. 
Isso melhora a clareza dos retornos e a experiência de quem consome a aplicação. 

poderia utilizar:
   - 204 para retornos com sucesso sem conteúdo
   - 201 criação de recursos 
*/

class AccountController extends Controller
{
    /**
     * Ativa a conta bancária
     *
     * POST api/users/{id}/account
     *
     * @return JsonResponse
     */
    public function register(string $userId): JsonResponse
    {
        (new Register($userId, Auth::user()->company_id))->handle();

        return $this->response(
            new DefaultResponse()
        );
    }

    /**
     * Desbloqueia a conta bancária
     *
     * POST api/users/{id}/account/active
     *
     * @return JsonResponse
     */
    public function active(string $userId): JsonResponse
    {
        (new Active($userId))->handle();

        return $this->response(
            new DefaultResponse()
        );
    }

    /**
     * Bloqueia a conta bancária
     *
     * POST api/users/{id}/account/block
     *
     * @return JsonResponse
     */
    public function block(string $userId): JsonResponse
    {
        (new Block($userId))->handle();

        return $this->response(
            new DefaultResponse()
        );
    }

    /**
     * Obtem os dados da conta
     *
     * GET api/users/{id}/account
     *
     * @return JsonResponse
     */
    public function show(string $userId): JsonResponse
    {
        $response = (new Show($userId))->handle();

        return $this->response(
            new DefaultResponse(
                new ShowResource($response)
            )
        );
    }
}
