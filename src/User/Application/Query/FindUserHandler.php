<?php

namespace Src\User\Application\Query;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Src\User\Domain\Repository\UserRepository;

class FindUserHandler extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handle(Request $request, string $id)
    {
        return $this->userRepository->find($id);
    }
}
