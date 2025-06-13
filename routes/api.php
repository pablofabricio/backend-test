<?php

/* 

AJUSTES:

1. Separação das rotas em arquivos por domínio, o que facilita a manutenção e visualização. 

Exemplo: users.php, companies.php, accounts.php e card.php

2. Deixar rotas para accounts e card de forma direta para não gerar acoplamento e complexidade sem necessidade. 
Pegar id do user pelo que já está logado.

Exemplo: {url}/accounts e {url}/cards

3. Nas rotas de ativar e bloquear conta poderia utilizar método patch por ser uma atualização parcial

*/

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\HealthCheckController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Healthcheck
Route::get('healthcheck', [HealthCheckController::class, 'healthCheck']);

// Users (Rota Pública)
Route::prefix('users')->group(function () {
    Route::post('register', [UserController::class, 'register']);

    Route::post('login', [UserController::class, 'login'])->middleware('auth.basic');
});

Route::group(['middleware' => ['auth:sanctum', 'policies.app']], function () {
    // Companies
    Route::prefix('company')->group(function () {
        Route::get('', [CompanyController::class, 'show']);

        Route::patch('', [CompanyController::class, 'update']);
    });

    // Users
    Route::prefix('users')->group(function () {
        Route::get('', [UserController::class, 'index']);

        Route::get('{id}', [UserController::class, 'show']);

        Route::post('', [UserController::class, 'create']);

        Route::patch('{id}', [UserController::class, 'update']);

        // Accounts
        Route::prefix('{id}/account')->group(function () {
            Route::get('', [AccountController::class, 'show']);

            Route::put('active', [AccountController::class, 'active']);

            Route::put('block', [AccountController::class, 'block']);

            Route::post('register', [AccountController::class, 'register']);
        });

        Route::prefix('{id}/card')->group(function () {
            Route::get('', [CardController::class, 'show']);

            Route::post('register', [CardController::class, 'register']);
        });
    });
});
