<?php

// sugestão: utilização do 'declare(strict_types=1);',
// é uma boa prática, assim evitando conversões automáticas de tipo para uma maior segurança e evitar bugs

namespace App\Domains\Card;

use App\Domains\BaseDomain;
use App\Repositories\Account\FindByUser;
use App\Exceptions\InternalErrorException;
use App\Repositories\Card\CanUseExternalId;

class Register extends BaseDomain
{
    // sugestão: remover os comentários autoexplicativos.
    /**
     * Id da conta
     *
     * @var string
     */
    protected string $accountId;

    /**
     * Id do usuário
     *
     * @var string
     */
    protected string $userId;

    /**
     * Id do cartão
     *
     * @var string
     */
    protected string $cardId;

    /**
     * PIN do cartão
     *
     * @var string
     */
    protected string $pin;

    public function __construct(string $userId, string $pin, string $cardId)
    {
        // sugestão: seguir com a identação simples (PSR-12)
        $this->userId = $userId;
        $this->pin    = $pin;
        $this->cardId = $cardId;
    }

    /**
     * Busca o id de conta
     *
     * @return void
     */
    protected function findAccountId(): void
    {
        // sugestão: incluir um layer service para remover o acesso do domain ao repository
        $account = (new FindByUser($this->userId))->handle();

        if (is_null($account)) {
            throw new InternalErrorException(
                'ACCOUNT_NOT_FOUND', // sugestão: incluir uma mensagem amigável
                161001001 // sugestão: mover esse código para um enum correspondente
            );
        }

        // sugestão: validar se a chave existe
        $this->accountId = $account['id'];
    }

    /**
     * Cartão não pode já estar vinculado
     */
    // sugestão: incluir o retorno do método,
    // outro ponto seria evitar retorno void para melhorar a cobertura de assertividade nos testes
    protected function checkExternalId()
    {
        if (!(new CanUseExternalId($this->cardId))->handle()) {
            throw new InternalErrorException(
                'Não é possível vincular esse cartão',
                0
            );
        }
    }

    /**
     * Checa se é possível vincular o cartão
     *
     * @return self
     */
    public function handle(): self
    {
        $this->findAccountId();
        $this->checkExternalId();

        return $this;
    }
}
