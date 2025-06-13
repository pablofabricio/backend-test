<?php

namespace App\Repositories\Card;

use App\Models\Card;
use App\Repositories\BaseRepository;

class CanUseExternalId extends BaseRepository
{
    /**
     * Id externo
     *
     * @var string
     */
    protected string $externalId;

    /**
     * Setar a model do cartão
     *
     * @return void
     */
    public function setModel(): void
    {
        $this->model = Card::class;
    }

    /**
     * Sugestão:
     * Como o projeto utiliza o php na versão 8, é possível utilizar property promotion
     */

    public function __construct(string $externalId)
    {
        $this->externalId = $externalId;

        parent::__construct();
    }

    /**
     * Valida se o documento é único
     *
     * @return bool
     */
    public function handle(): bool
    {
        $user = $this->builder
            ->where('external_id', $this->externalId)
            ->first();

        return is_null($user);
    }
}
