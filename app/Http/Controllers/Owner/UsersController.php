<?php

namespace App\Http\Controllers\Owner;
use ErrorException;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Owner\UsersService;

class UsersController extends Controller
{
    protected $user;

    public function __construct(UsersService $user)
    {
        $this->user = $user;
    }

    /**
     * Get all user
     * 
     * @return mixed
     */
    public function user()
    {
        try {
            return $this->user->getAllUsers();
        } catch (ErrorException $e) {
          throw new ErrorException($e->getMessage());
        }
    }
}
