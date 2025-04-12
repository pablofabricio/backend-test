<?php

namespace App\Domains\Company;

use App\Domains\BaseDomain;
use App\Exceptions\InternalErrorException;
use App\Repositories\Company\CanUseDocumentNumber;

class Create extends BaseDomain
{
    /**
     * Nome
     *
     * @var string
     */
    protected string $name;

    /**
     * CNPJ
     *
     * @var string
     */
    protected string $documentNumber;

    public function __construct(string $name, string $documentNumber)
    {
        $this->name           = $name;
        $this->documentNumber = $documentNumber;
    }

    /**
     * Documento de empresa deve ser único no sistema
     */
    protected function checkDocumentNumber()
    {
        //PM1
        //PM2
        if (!(new CanUseDocumentNumber($this->documentNumber))->handle()) {
            throw new InternalErrorException(
                'Não é possível adicionar o CNPJ informado',
                0
            );
        }
    }

    /**
     * Checa se é possível criar a empresa
     *
     * @return self
     */
    public function handle(): self
    {
        //Renomear para CheckUniqueDocumentNumber para facilitar o entendimento do que é um document number válido.
        $this->checkDocumentNumber();

        return $this;
    }
}
