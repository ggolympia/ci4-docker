<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class MarketController extends ResourceController
{
    protected $modelName = 'App\Models\Cruises_model';  // Your model for fetching data
    protected $format    = 'json';

    public function index()
    {
        $filters = $this->request->getGet();  // Retrieve filter parameters from the query string
        $page = $this->request->getGet('page') ?? 1;  // Get the page number from the query string
        $limit = 50;  // Set limit of 50 items per page
        $offset = ($page - 1) * $limit;

        // Fetch items and total count from the model
        $result = $this->model->getFilteredItems($filters, $limit, $offset);

        // Fetch filter component data (e.g., price, exterior, etc.)
        $filterComponents = $this->getFilterComponents();

        // Prepare the response payload
        $response = [
            'requestId' => (string) \Ramsey\Uuid\Guid\Guid::uuid4(),  // Generating a new requestId
            'success' => true,
            'message' => null,
            // 'filter' => [
            //     'components' => $filterComponents,
            // ],
            'filters' => $filters,
            'total' => $result['total'],
            'currentPage' => (int) $page,
            'totalPages' => ceil($result['total'] / $limit),
            'items' => $result['items'],
        ];

        return $this->respond($response);
    }

    // Example method to get filter components
    private function getFilterComponents()
    {
        return [
            ['name' => 'price', 'type' => 'priceBar', 'data' => []],
            ['name' => 'exterior', 'type' => 'selectionWithBar', 'data' => [
                ['key' => 2, 'value' => 'Factory New'],
                ['key' => 4, 'value' => 'Minimal Wear'],
                ['key' => 3, 'value' => 'Field-Tested'],
                // Other values...
            ]],
            ['name' => 'other', 'type' => 'multiSelection', 'data' => [
                ['key' => 'stattrak', 'value' => 'StatTrak™'],
                ['key' => 'souvenir', 'value' => 'Souvenir'],
                // Other values...
            ]],
            // Add more components here as needed...
        ];
    }
}
