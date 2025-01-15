<?php

namespace App\Controllers;

use App\Models\Passenger_model;
use CodeIgniter\RESTful\ResourceController;

class PassengersController extends ResourceController
{
    protected $modelName = 'App\Models\Passenger_model';
    protected $format = 'json';

    public function getPassengers()
    {
        // Example API call to fetch homepage slider items
        // Example API call to fetch homepage slider items
        $passenger = new \App\Models\Passenger_model();
        $guests = $passenger->getPassengers();

        return $this->respond($guests);
    }

    // Method to get passenger by ID
    public function get($id)
    {
        $passenger = $this->model->getPassenger($id);
        if ($passenger) {
            return $this->respond($passenger);
        } else {
            return $this->failNotFound('Passenger not found');
        }
    }

    public function create()
    {
        $data = $this->request->getJSON();


        // Check if data is empty
        if (empty($data)) {
            return $this->failValidationErrors('No data provided');
        }

        // Attempt to create the passenger
        if ($this->model->createPassenger($data)) {
            return $this->respondCreated(['message' => 'Passenger created successfully']);
        } else {
            return $this->failValidationErrors('Error creating passenger');
        }
    }


    // Method to update passenger details

    // Method to mark a passenger as inactive
    public function deactivate($id = null)
    {
        if ($this->model->setInactive($id)) {
            return $this->respond(['message' => 'Passenger marked as inactive']);
        } else {
            return $this->failNotFound('Passenger not found');
        }
    }

    // Method to update passenger details
    public function update($id = null)
    {
        $data = $this->request->getJSON();  // Get the JSON data from the request

        if (!$data) {
            return $this->fail('No data received', 400);
        }

        $passenger = $this->model->getPassenger($id); // Find the passenger by ID

        if (!$passenger) {
            return $this->failNotFound('Passenger not found');
        }

        // Prepare the data for update
        $updatedData = [
            'first_name'   => $data->first_name,
            'last_name'    => $data->last_name,
            'email'       => $data->email,
            'mobile_number' => $data->mobile_number,
            'wheelchair'  => $data->wheelchair,
        ];

        // Update the passenger's data
        $this->model->updatePassenger($id, $updatedData);

        return $this->respondUpdated($updatedData);
    }
}
