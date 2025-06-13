<?php

namespace App\Domains\User;

use App\Domains\BaseDomain;
use Illuminate\Support\Facades\Hash;
use App\Repositories\User\CanUseEmail;
use App\Exceptions\InternalErrorException;

class Update extends BaseDomain
{
    /**
     * Id do usuário
     *
     * @var string
     */
    protected string $id;

    /**
     * Empresa
     *
     * @var string
     */
    protected string $companyId;

    /**
     * Nome
     *
     * @var string|null
     */
    protected ?string $name;

    /**
     * Email
     *
     * @var string|null
     */
    protected ?string $email;

    /**
     * Senha
     *
     * @var string|null
     */
    protected ?string $password;

    /**
     * Tipo
     *
     * @var string|null
     */
    protected ?string $type;

    /**
     * Sugestão:
     * Alterar a identação seguindo a PSR-12
     */

    public function __construct(
        string $id,
        string $companyId,
        ?string $name,
        ?string $email,
        ?string $password,
        ?string $type
    ) {
        $this->id        = $id;
        $this->companyId = $companyId;
        $this->name      = $name;
        $this->email     = $email;
        $this->type      = $type;

    /**
     * Melhoria: remover lógica de dentro do construtor
     */
        $this->cryptPassword($password);
    }

    /**
     * Encripta a senha
     *
     * @param string|null $password
     *
     * @return void
     */
    protected function cryptPassword(?string $password): void
    {
        $this->password = !is_null($password) ? Hash::make($password) : null;
    }

    /**
     * Email deve ser únicos no sistema
     *
     * @return void
     */
    protected function checkEmail(): void
    {
        if (is_null($this->email)) {
            return;
        }
        if (!(new CanUseEmail($this->email))->handle()) {
            throw new InternalErrorException(
                'Não é possível adicionar o E-mail informado',
                0
            );
        }
    }

    /**
     * Valida o tipo
     *
     * @return void
     */

    /**
     * Sugestão: extrair os tipos para enums, fazendo com que fique mais fácil acrescentar um novo tipo posteriormente
     */

    protected function checkType(): void
    {
        if (is_null($this->type)) {
            return;
        }
        if (!in_array($this->type, ['USER', 'VIRTUAL', 'MANAGER'])) {
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
        $this->checkType();

        return $this;
    }
}
