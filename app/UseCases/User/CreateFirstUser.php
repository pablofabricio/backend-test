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

    // public static function make(array $params): self
    // {
    //     return new self(
    //         new CreateFirstUserParams(
    //             $params['companyName'],
    //             $params['companyDocumentNumber'],
    //             $params['userName'],
    //             $params['userDocumentNumber'],
    //             $params['email'],
    //             $params['password']
    //         )
    //     );
    // }


    public function __construct(
        CreateFirstUserParams $params
    ) {
        $this->params = $params;
    }

    /**
     * Valida a empresa
     */
    protected function validateCompany(): CreateCompanyDomain
    {
        return (new CreateCompanyDomain(
            $this->params->companyName,
            $this->params->companyDocumentNumber
        ))->handle();
    }

    //PA1
    //Retorno desta função deveria ser um array e nao um void.
    /**
     * Cria a empresa
     *
     * @param CreateCompanyDomain $domain
     *
     * @return void
     */
    protected function createCompany(CreateCompanyDomain $domain): void
    {
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
    //PM - Seria interessante adicionar um DTO como type ao retorno desta função, principalmente pelo nome sugerir
    //que ele retorna um user quando na verdade retorna um usuário, empresa e token.
    public function handle()
    {
        //PA3 - Da forma como esta sendo realizado o fluxo pode ocorrer de uma companhia ser criada mesmo
        // no caso falho onde a verificação de usuário falha. Alterar as para que as verificações sejam
        //feitas antes do create ou adicionar uma transaction.
        try {
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
