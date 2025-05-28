<?php

namespace App\Repositories\Token;

use App\Models\User;

/**
 * Esse repository não extende de BaseRepository, fudindo do padrão dos outros.
 */
class Create
{
    /**
     * Id do usuário
     *
     * @var string
     */
    protected string $id;

    /**
     * Permissões
     *
     * @var array
     */
    protected array $permissions;

    /**
     * Model base para implementação
     *
     * @var string
     */
    protected string $model;

    public function __construct(string $id, array $permissions = [])
    {
        $this->id          = $id;
        $this->permissions = $permissions;
        $this->model       = User::class;
    }

    /**
     * Criação de token de acesso do usuário
     *
     * @return string
     */
    public function handle(): string
    {
        return app($this->model)
            ->findOrFail($this->id)
            ->createToken(config('auth.token_name'), $this->permissions)
            ->plainTextToken;
    }
}
