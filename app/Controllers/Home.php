<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;

class Home extends BaseController
{
    use ResponseTrait;

    public function index(): string
    {
        return view('welcome_message');
    }


    public function hello()
    {
        $data = ['message' => 'Hello, World!'];
        echo ENVIRONMENT;
        //add the header here
         return $this->respond($data);
    }
}
