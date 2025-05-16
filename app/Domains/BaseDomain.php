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
    public function __get(string $prop): mixed
    {
        // sugestão: validar se a propriedade existe na classe caso não retornar null evitando erro
        // outra sugestão é criar um accessors recuperando os dados como função get{$propName}()
        return $this->{$prop};
    }

}
