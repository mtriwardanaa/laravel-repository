<?php

use Src\User\Application\Command\StoreUserHandler;
use Src\User\Application\Query\FindUserHandler;
use Src\User\Application\Query\ListUserHandler;

Route::get('/', [ListUserHandler::class, 'handle']);
Route::get('/{id}', [FindUserHandler::class, 'handle']);
Route::post('/', [StoreUserHandler::class, 'handle']);
