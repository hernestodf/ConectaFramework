<?php

namespace App\Controllers;

use App\Http\Controller;
use App\Core\Response;
use App\Auth\Rbac;

class AdminController extends Controller
{
    public function index(): Response
    {
        return $this->view('admin/index', [
            'title' => 'Admin - NovoFramework',
            'user' => Rbac::getUser(),
        ]);
    }
}