<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Responses\InternalError;

/*

AJUSTES:

7. Nunca retornar status 200 em exceção como tem a possibilidade na linha 77, mas usar uma constante do laravel como Response::HTTP_INTERNAL_SERVER_ERROR

*/

class BaseException extends \Exception
{
    /**
     * Lista de erros
     *
     * @var array
     */
    private array $errors = [];

    /**
     * Código da response
     *
     * @var int|null
     */
    private int|null $responseCode = null;

    /**
     * Definir o response code da request
     *
     * @param int $responseCode
     * @return self
     */
    public function defineResponseCode(int $responseCode): self
    {
        $this->responseCode = $responseCode;

        return $this;
    }

    /**
     * Adiciona à lista de erros um InternalError
     *
     * @param string $message
     * @param int|null $code
     * @param string|null $log
     * @param string|null $moreInfo
     *
     * @return self
     */
    public function setError(
        string $message,
        int $code = null,
        string $log = null,
        string $moreInfo = null
    ): self {
        $this->errors[] = new InternalError(
            $message,
            $code,
            $log,
            $moreInfo
        );

        return $this;
    }

    /**
     * Formata a resposta que será devolvida para o usuário
     */
    public function render(Request $request): JsonResponse
    {
        $responseCode = !is_null($this->responseCode) ? $this->responseCode : 200;

        return response()->json(
            [
                'success' => false,
                'request' => $request->fullUrl(),
                'method'  => strtoupper($request->method()),
                'code'    => $responseCode,
                'data'    => null,
                'errors'  => $this->errors,
            ],
            status: $responseCode
        );
    }
}
