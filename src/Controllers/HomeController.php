<?php

namespace App\Controllers;

use App\Http\Controller;
use App\Core\Response;
use App\Database\Connection;
use App\Auth\Rbac;

class HomeController extends Controller
{
    public function index(): Response
    {
        $connectionTest = Connection::testConnection();
        
        $users = [];
        if ($connectionTest['success']) {
            $users = Connection::query("SELECT id, name, email, role, status, created_at FROM users ORDER BY id LIMIT 10");
        }
        
        return $this->view('home/index', [
            'title' => 'Dashboard - NovoFramework',
            'connection' => $connectionTest,
            'users' => $users,
            'user' => Rbac::getUser(),
        ]);
    }

    public function test(): Response
    {
        $connectionTest = Connection::testConnection();
        $env = Connection::getEnvironment();
        
        return $this->json([
            'status' => 'ok',
            'message' => 'Hello World!',
            'environment' => $env,
            'database' => $env === 'local' ? 'DB_LOCAL' : 'DB_ONLINE',
            'connection' => $connectionTest,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }
}