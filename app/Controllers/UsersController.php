<?php

namespace App\Controllers;

use App\Models\Users_model;
use CodeIgniter\API\ResponseTrait;

class UsersController extends BaseController
{
    public function index()
    {
        $data = [];
        $usersModel = new Users_model();
        $data['users'] = $usersModel->getUsers();
        return view('itineraries/index', $data);
    }

    use ResponseTrait;
}
