<?php

namespace App\Domains\User;

use App\Domains\BaseDomain;
use Illuminate\Support\Facades\Hash;
use App\Repositories\User\CanUseEmail;
use App\Exceptions\InternalErrorException;
use App\Repositories\User\CanUseDocumentNumber;

class Create extends BaseDomain
{
    /**
     * Empresa
     *
     * @var string
     */
    protected string $companyId;

    /**
     * Nome
     *
     * @var string
     */
    protected string $name;

    /**
     * CPF
     *
     * @var string
     */
    protected string $documentNumber;

    /**
     * Email
     *
     * @var string
     */
    protected string $email;

    /**
     * Senha
     *
     * @var string
     */
    protected string $password;

    /**
     * Tipo
     *
     * @var string
     */
    protected string $type;

    public function __construct(
        string $companyId,
        string $name,
        string $documentNumber,
        string $email,
        string $password,
        string $type
    ) {
        $this->companyId      = $companyId;
        $this->name           = $name;
        $this->documentNumber = $documentNumber;
        $this->email          = $email;
        $this->type           = $type;

        $this->cryptPassword($password);
    }

    /**
     * Encripta a senha
     *
     * @param string $password
     *
     * @return void
     */
    protected function cryptPassword(string $password): void
    {
        $this->password = Hash::make($password);
    }

    /**
     * Email deve ser únicos no sistema
     *
     * @return void
     */
    protected function checkEmail(): void
    {
        if (!(new CanUseEmail($this->email))->handle()) {
            /**
             * Usar uma exceção mais recomendada para essa validação como \InvalidArgumentException
             */

            /**
             * Estudar a possibilidade de colocar uma mensagem mais clara do erro como (Email já existente),
             * porém dependendo da estratégia pode não ser uma boa idéia expor que já existe essa informação na base.
             */
            throw new InternalErrorException(
                'Não é possível adicionar o E-mail informado',
                0
            );
        }
    }

    /**
     * Email deve ser únicos no sistema
     *
     * @return void
     */
    protected function checkDocumentNumber(): void
    {
        if (!(new CanUseDocumentNumber($this->documentNumber))->handle()) {
            /**
             * Usar uma exceção mais recomendada para essa validação como \InvalidArgumentException
             */

            /**
             * Estudar a possibilidade de colocar uma mensagem mais clara do erro como (CPF já existente),
             * porém dependendo da estratégia pode não ser uma boa idéia expor que já existe essa informação na base.
             */
            throw new InternalErrorException(
                'Não é possível adicionar o CPF informado',
                0
            );
        }
    }

    /**
     * Valida o tipo
     *
     * @return void
     */
    protected function checkType(): void
    {
        if (!in_array($this->type, ['USER', 'VIRTUAL', 'MANAGER'])) {
            /**
             * Usar uma exceção mais recomendada para essa validação como \InvalidArgumentException
             */

             /**
              * Colocar mensagem mais clara do erro, por exemplo: Tipo informado inválido. Tipos aceitos: ['USER', 'VIRTUAL', 'MANAGER']
              */
            throw new InternalErrorException(
                'Não é possível adicionar o tipo informado',
                0
            );
        }
    }

    /**
     * Checa se é possível realizar a criação do usuário
     *
     * @return self
     */
    public function handle(): self
    {
        $this->checkEmail();
        $this->checkDocumentNumber();
        $this->checkType();

        return $this;
    }
}
