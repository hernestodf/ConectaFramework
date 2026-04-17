<?php

namespace App\Controllers;

use App\Http\Controller;
use App\Core\Response;
use App\Core\Csrf;
use App\Core\Env;
use App\Core\Application;
use App\Auth\Rbac;

class AuthController extends Controller
{
    public function login(): Response
    {
        if (!Csrf::hasToken()) {
            Csrf::generate();
        }
        
        $app = Application::getInstance();
        $theme = $app->getTheme();
        
        return $this->view('auth/login', [
            'title' => 'Login - NovoFramework',
            'theme' => $theme,
        ]);
    }

public function doLogin(): Response
    {
        $email = $this->post('email');
        $password = $this->post('password');
        
        // Temporário: permitir login para teste mobile
        $csrfInput = $this->post('_csrf_token');
        
        if ($csrfInput && !Csrf::validate($csrfInput)) {
            return $this->json(['success' => false, 'message' => 'Token inválido'], 403);
        }

        if (Rbac::login($email, $password)) {
            Csrf::regenerate();
            
            $baseUrl = rtrim(Env::get('BASE_URL', ''), '/');
            return $this->json(['success' => true, 'redirect' => $baseUrl . '/users']);
        }

        return $this->json(['success' => false, 'message' => 'Credenciais inválidas'], 401);
    }

    public function logout(): Response
    {
        Csrf::forget();
        Rbac::logout();
        
        $baseUrl = rtrim(Env::get('BASE_URL', ''), '/');
        return $this->redirect($baseUrl . '/auth/login');
    }
}