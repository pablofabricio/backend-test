<?php

namespace App\Integrations\Banking\Account;

use App\Integrations\Banking\Gateway;
use App\Repositories\Account\FindByUser;
use App\Exceptions\InternalErrorException;

class Find extends Gateway
{
    /**
     * Id do usuário
     *
     * @var string
     */
    protected string $userId;

    /**
     * Id externo da conta
     *
     * @var string
     */
    protected string $externalId;

    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Apesar do nome do método dar a entender que busca os dados de uma conta, tudo que ele faz é setar o external_id da conta.
     * A nomenclatura ideal seria setExternalId().
     * Além disso o ideal é que essa função esteja na camada de UseCases e passe o externalId via injeção de dependência por exemplo, 
     * já que é um parâmetro imprescindível para o funcionamento dessa integração.
     */
    /**
     * Busca os dados de conta
     *
     * @return void
     */
    protected function findAccountData(): void
    {
        $account = (new FindByUser($this->userId))->handle();

        /**
         * Definir o código de exceção 161001001 em uma constante para ajudar a entender do que o mesmo se trata.
         * A exception mais adequada 
         */
        if (is_null($account)) {
            throw new InternalErrorException(
                'ACCOUNT_NOT_FOUND',
                161001001
            );
        }

        $this->externalId = $account['external_id'];
    }

    /**
     * Constroi a url da request
     *
     * @return string
     */
    protected function requestUrl(): string
    {
        return "accounts/$this->externalId";
    }

    /**
     * Descrição do método não reflete o que o mesmo faz.
     */
    /**
     * Modifica o status de uma conta
     *
     * @return array
     */
    public function handle(): array
    {
        $this->findAccountData();
        $url = $this->requestUrl();

        /**
         * O nome mais adequado para a variável $request seria $response, pois se trata de uma resposta da API.
         */
        $request = $this->sendRequest(
            method: 'get',
            url:    $url,
            action: 'FIND_ACCOUNT',
            params: []
        );

        return $this->formatDetailsResponse($request);
    }
}
