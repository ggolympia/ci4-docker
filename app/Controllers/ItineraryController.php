<?php

namespace App\Controllers;

use App\Models\Itinerary_model;

class ItineraryController extends BaseController
{
    public function index()
    {
        $data = [];
        $itineraryModel = new Itinerary_model();
        $data['itineraries'] = $itineraryModel->getItineraries();
        return view('itineraries/index', $data);
    }
    public function getCruiseByCruiselineID($cruiseLineSlug, $cruiseID)
    {
        // Fetch data based on 'cruiseLineSlug' (e.g., 'royal-caribbean') and 'cruiseID' (e.g., 499)
        $itineraryModel = new Itinerary_model();
        $cruiseData = $itineraryModel->getCruiseData($cruiseLineSlug, $cruiseID);

        // Check if cruise data was found
        if ($cruiseData) {
            // Return the cruise data as a JSON response
            return $this->response->setJSON($cruiseData);
        } else {
            // Return a 404 response with a message if no cruise data is found
            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'Cruise not found'
            ]);
        }
    }
}
