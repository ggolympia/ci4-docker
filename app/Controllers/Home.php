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
        $data = ['message' => 'Hello, Worlds!'];
        echo 'Testing Crazy';
        //add the header here
         return $this->respond($data);
    }
}
