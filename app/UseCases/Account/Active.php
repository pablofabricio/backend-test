<?php

namespace App\UseCases\Account;

use Throwable;
use App\UseCases\BaseUseCase;
use App\Repositories\Account\UpdateStatus as RepositoryUpdateStatus;
use App\Integrations\Banking\Account\UpdateStatus as IntegrationUpdateStatus;

class Active extends BaseUseCase
{
    /**
     * Id do usuário
     *
     * @var string
     */
    protected string $userId;

    /**
     * Conta
     *
     * @var array
     */
    protected array $account;


    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Atualiza no banco de dados
     *
     * @return void
     */
    protected function updateDatabase(): void
    {
        (new RepositoryUpdateStatus($this->userId, 'active'))->handle();
    }

    /**
     * Atualiza a conta
     *
     * @return void
     */
    protected function updateStatus(): void
    {
        $this->account = (new IntegrationUpdateStatus($this->userId, 'active'))->handle();
    }

    /**
     * Ativa a conta
     */
    public function handle(): void
    {
        //PA3 Seria importante alterar a ordem de chamada das funções para garantir que a alteração
        //local so seja realizada com o sucesso da chamada externa.
        try {
            $this->updateDatabase();
            $this->updateStatus();
        } catch (Throwable $th) {
            $this->defaultErrorHandling(
                $th,
                [
                    'userId' => $this->userId,
                ]
            );
        }
    }
}
