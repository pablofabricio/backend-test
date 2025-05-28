<?php

namespace App\UseCases\User;

use Throwable;
use App\UseCases\BaseUseCase;
use App\Domains\User\Create as CreateUserDomain;
use App\Repositories\Token\Create as CreateToken;
use App\UseCases\Params\User\CreateFirstUserParams;
use App\Domains\Company\Create as CreateCompanyDomain;
use App\Repositories\User\Create as CreateUserRepository;
use App\Repositories\Company\Create as CreateCompanyRepository;

class CreateFirstUser extends BaseUseCase
{
    /**
     * @var CreateFirstUserParams
     */
    protected CreateFirstUserParams $params;

    /**
     * Token de acesso
     *
     * @var string
     */
    protected string $token;

    /**
     * Empresa
     *
     * @var array
     */
    protected array $company;

    /**
     * Usuário
     *
     * @var array
     */
    protected array $user;

    public function __construct(
        CreateFirstUserParams $params
    ) {
        $this->params = $params;
    }

    /**
     * Valida a empresa
     *
     * @return CreateCompanyDomain
     */
    protected function validateCompany(): CreateCompanyDomain
    {
        return (new CreateCompanyDomain(
            $this->params->companyName,
            $this->params->companyDocumentNumber
        ))->handle();
    }

    /**
     * Cria a empresa
     *
     * @param CreateCompanyDomain $domain
     *
     * @return void
     */
    protected function createCompany(CreateCompanyDomain $domain): void
    {
        /**
         * Vou comentar sobre isso apenas aqui para não ficar redundante, mas isso é valido para todos os lugares que utilizam os repositories para interação com banco de dados.
         * 
         * Complementando as sugestões que eu já coloquei no arquivo app\Repositories\BaseRepository.php
         * Na aplicação como um todo claramente ao usar os repositories existentes, se cria uma dependência entre os casos de uso e infraestrutura,
         * pois os repositories dependem diretamente do Eloquent ORM, ferindo assim os conceitos de arquiteturas como Clean Arch e DDD 
         * que enfatizam a independência da camada de lógica de negócio da aplicação e infraestrutura.
         * Portanto o recomendado é que esses repositories cheguem até aqui através de injeção de dependência, 
         * porém nessa injeção de dependência colocar a tipagem de uma interface genérica dessa iteração, então o código ficaria algo parecido com:
         * $this->company = $this->createCompanyRepository->handle();
         * sendo que $this->createCompanyRepository receberia em seu construtor uma tipagem da interface CreateCompanyRepositoryInterface
         */
        $this->company = (new CreateCompanyRepository($domain))->handle();
    }

    /**
     * Valida o usuário
     *
     * @return CreateUserDomain
     */
    protected function validateUser(): CreateUserDomain
    {
        return (new CreateUserDomain(
            $this->company['id'],
            $this->params->userName,
            $this->params->userDocumentNumber,
            $this->params->email,
            $this->params->password,
            'MANAGER'
        ))->handle();
    }

    /**
     * Cria o usuário
     *
     * @param CreateUserDomain $domain
     *
     * @return void
     */
    protected function createUser(CreateUserDomain $domain): void
    {
        $this->user = (new CreateUserRepository($domain))->handle();
    }

    /**
     * Criação de token de acesso
     *
     * @return void
     */
    protected function createToken(): void
    {
        $this->token = (new CreateToken($this->user['id']))->handle();
    }

    /**
     * Cria um usuário MANAGER e a empresa
     */
    public function handle()
    {
        try {
            /**
             * Como envolve persistência de dados de mais de uma tabela, usar controle de transação ->beginTransaction e usar ->commit em caso de sucesso e ->rollback em caso de exceção.
             */
            $companyDomain = $this->validateCompany();
            $this->createCompany($companyDomain);
            $userDomain = $this->validateUser();
            $this->createUser($userDomain);
            $this->createToken();
        } catch (Throwable $th) {
            $this->defaultErrorHandling(
                $th,
                [
                    'params' => $this->params->toArray(),
                ]
            );
        }

        return [
            'user'    => $this->user,
            'company' => $this->company,
            'token'   => $this->token,
        ];
    }
}
