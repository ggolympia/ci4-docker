<?php

namespace App\Controllers;

use CodeIgniter\HTTP\Response;


class MaintenanceController extends BaseController
{
//    private $allowedPassword;

    private $allowedPassword = 'garzon'; // Set your password here

    public function setAccessCookie()
    {
        $input = $this->request->getJSON();
        $password = $input->password ?? '';

        if ($password === $this->allowedPassword) {
            // Set a secure cookie to grant access
            $cookie = [
                'name'     => 'site_access',
                'value'    => 'granted',
                'expire'   => 3600, // 1 hour
                'secure'   => true,
                'httponly' => true,
                'path'     => '/',
            ];
            setcookie(
                $cookie['name'],
                $cookie['value'],
                time() + $cookie['expire'],
                $cookie['path'],
                '',
                $cookie['secure'],
                $cookie['httponly']
            );

            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Invalid password'])->setStatusCode(401);
    }

    public function validateAccess() {
        log_message('info', 'Incoming Cookies: ' . json_encode($this->request->getCookie()));
        $cookie = $this->request->getCookie('site_access');
        if ($cookie === 'granted') {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setStatusCode(401)->setJSON(['success' => false]);
        }
    }

}
