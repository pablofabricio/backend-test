<?php

namespace Tests;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use WithFaker;
    use CreatesApplication;
    /**
     * Estudar troca para a trait RefreshDatabase que já executa migrations automaticamente no ambiente de testes automatizados.
     */
    use DatabaseTransactions;
}
