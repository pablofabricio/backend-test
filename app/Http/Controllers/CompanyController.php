<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\UseCases\Company\Show;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Responses\DefaultResponse;
use App\Http\Requests\Company\UpdateRequest;
use App\Http\Resources\Company\ShowResource;
use App\Http\Resources\Company\UpdateResource;
use App\Domains\Company\Update as UpdateDomain;
use App\Repositories\Company\Update as CompanyUpdate;

class CompanyController extends Controller
{
    /**
     * Endpoint de dados de empresa
     *
     * GET api/company
     *
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        $response = (new Show(Auth::user()->company_id))->handle();

        return $this->response(
            new DefaultResponse(
                new ShowResource($response)
            )
        );
    }

    /**
     * Endpoint de modificação de empresa
     *
     * PATCH api/company
     *
     * @return JsonResponse
     */
    public function update(UpdateRequest $request): JsonResponse
    {
        //PA4
        //Essa função do controller deveria chamar o useCase de update de company que chamaria o find do
        //repository de company
        //PA3
        //O updateDomain não será necessário uma vez que não existem validações.
        //PA2
        //A nomeação das variáveis $dominio e resposta em português fogem ao padrão utilizado no projeto.
        //PA1
        //Após o find na empresa o name recebido no request não é realmente atualizado
        $dominio = (new UpdateDomain(
            Auth::user()->company_id,
            $request->name,
        ))->handle();

        (new CompanyUpdate($dominio))->handle();

        $resposta = Company::find(Auth::user()->company_id)->first()->toArray();

        return $this->response(
            new DefaultResponse(
                new UpdateResource($resposta)
            )
        );
    }
}
