<?php

namespace App\Controllers;

use App\Models\UserSessions_model;
use CodeIgniter\RESTful\ResourceController;

class UserSessionsController extends ResourceController
{
    protected $modelName = 'App\Models\UserSessions_model';
    protected $format = 'json';

    public function create()
    {
        $data = $this->request->getJSON();

        if ($this->model->insert($data)) {
            return $this->respondCreated(['message' => 'User session created successfully.']);
        } else {
            return $this->failValidationErrors($this->model->errors());
        }
    }

    public function index()
    {
        return $this->respond($this->model->findAll());
    }

    public function getSessionsByID($user_id)
    {
        // Use where() to filter sessions by ID, then findAll() to get all matching records
        $userSessions = $this->model->where('user_id', $user_id)->findAll();

        if (empty($userSessions)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User Sessions not found'], 404);
        }

        return $this->response->setJSON($userSessions);
    }


    public function store()
    {
        $sessionData = $this->request->getJSON();

        if (!$sessionData) {
            return $this->fail('Invalid data provided.', 400);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('user_sessions');

        $insertData = [
            'user_id' => $sessionData->user_id ?? null, // Default to null if not set
            'browser' => $sessionData->browser ?? null,
            'city' => $sessionData->city ?? null,
            'state' => $sessionData->state ?? null,
            'ip_address' => $sessionData->ip_address ?? null,
            'hostname' => $sessionData->hostname ?? null, // Ensure that hostname exists
            'region' => $sessionData->region ?? null,
            'country' => $sessionData->country ?? null,
            'loc' => $sessionData->loc ?? null,
            'org' => $sessionData->org ?? null,
            'postal' => $sessionData->postal ?? null,
            'timezone' => $sessionData->timezone ?? null,
            'provider' => $sessionData->provider ?? null,
            'date_created' => date('Y-m-d H:i:s'),
            'date_updated' => date('Y-m-d H:i:s'),
        ];

        if ($builder->insert($insertData)) {
            return $this->respond(['status' => 'success', 'message' => 'Session added successfully.'], 201);
        } else {
            $error = $db->error(); // Retrieve database error
            log_message('error', 'Failed to insert user session: ' . json_encode($error)); // Log the error
            return $this->fail('Failed to add session. Error: ' . $error['message'], 500);
        }
    }
}
