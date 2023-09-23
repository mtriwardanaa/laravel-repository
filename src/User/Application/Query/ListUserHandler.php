<?php

namespace Src\User\Application\Query;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Src\User\Domain\Repository\UserRepository;

class ListUserHandler extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handle(Request $request)
    {
        return $this->userRepository->findAll();
    }
}
