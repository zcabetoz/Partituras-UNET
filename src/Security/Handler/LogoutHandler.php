<?php

namespace App\Security\Handler;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Http\Logout\LogoutUrlGenerator;

class LogoutHandler extends LogoutUrlGenerator
{
    public function logout(Request $request) {

    }
}