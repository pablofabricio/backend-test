<?php

namespace App\Repositories\Card;

use App\Models\Card;
use App\Domains\Card\Register;
use App\Repositories\BaseRepository;

class Create extends BaseRepository
{
    /**
     * Parâmetros de criação de cartão
     *
     * @var Register
     */
    protected Register $domain;

    /**
     * Setar a model do cartão
     *
     * @return void
     */
    public function setModel(): void
    {
        $this->model = Card::class;
    }

    public function __construct(Register $domain)
    {
        $this->domain = $domain;

        parent::__construct();
    }

    /**
     * Criação de usuário
     *
     * @return array
     */

    /**
     * Sugestão:
     * Acrescentar o valor ACTIVE como default na migration, assim não precisando passar o valor do status
     */

    public function handle(): array
    {
        return $this->create(
            [
                'account_id'  => $this->domain->accountId,
                'external_id' => $this->domain->cardId,
                'status'      => 'ACTIVE',
            ]
        );
    }
}
