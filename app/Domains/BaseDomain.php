<?php

namespace App\Domains;

use App\Traits\Instancer;

abstract class BaseDomain
{
    use Instancer;

    /**
     * Obter uma propriedade da classe
     *
     * @param string $prop
     *
     * @return mixed
     */

    /**
     * Sugestão:
     * Verificar se a propriedade existe antes de tentar acessá-la, assim evitando retornos com o valor null
     */

    public function __get(string $prop): mixed
    {
        return $this->{$prop};
    }
}
