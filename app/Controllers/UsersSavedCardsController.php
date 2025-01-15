<?php

namespace App\Controllers;

use App\Models\UsersSavedCards_model;

class UsersSavedCardsController extends BaseController
{
    protected $usersSavedCardsModel;

    public function __construct()
    {
        $this->usersSavedCardsModel = new UsersSavedCards_model();
    }

    // Create a new saved card
    public function create()
    {
        $data = $this->request->getJSON(true);

        // Remove spaces from the card_number field
        if (isset($data['card_number'])) {
            $data['card_number'] = str_replace(' ', '', $data['card_number']);
        }

        // Validate the input
        if (!$this->validate([
            'user_id'                => 'required|integer',
            'card_holder_first_name' => 'required|max_length[100]',
            'card_holder_last_name'  => 'required|max_length[100]',
            'billing_zip_code'       => 'required|max_length[10]',
            'expiration_date'        => 'required|regex_match[/^(0[1-9]|1[0-2])\/\d{2}$/]',
            'cvv'                    => 'required|numeric|exact_length[3,4]',
            'card_brand'             => 'required|max_length[50]',
            'card_number'            => 'required|numeric|exact_length[16]',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors(),
            ]);
        }

        // Hash sensitive data
        $cvvHash = password_hash($data['cvv'], PASSWORD_BCRYPT);
        $cardNumberHash = password_hash($data['card_number'], PASSWORD_BCRYPT);
        $lastFourNumbers = substr($data['card_number'], -4);

        // Check if the user already has a primary card
        $hasPrimaryCard = $this->usersSavedCardsModel
            ->where('user_id', $data['user_id'])
            ->where('is_primary', 1)
            ->countAllResults() > 0;

        // If no primary card exists, set this one as primary
        $isPrimary = $hasPrimaryCard ? 0 : 1;

        // Insert into the database
        $this->usersSavedCardsModel->insert([
            'user_id'                => $data['user_id'],
            'card_holder_first_name' => $data['card_holder_first_name'],
            'card_holder_last_name'  => $data['card_holder_last_name'],
            'billing_zip_code'       => $data['billing_zip_code'],
            'expiration_date'        => $data['expiration_date'],
            'cvv_hash'               => $cvvHash,
            'card_brand'             => $data['card_brand'],
            'card_number_hash'       => $cardNumberHash,
            'last_four_numbers'      => $lastFourNumbers,
            'is_primary'             => $isPrimary, // Set the primary flag dynamically
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Card saved successfully',
        ]);
    }


    // Fetch all saved cards for a user
    public function index($userId)
    {
        $cards = $this->usersSavedCardsModel->where('user_id', $userId)->findAll();
        return $this->response->setJSON($cards);
    }

    // Fetch a specific saved card
    public function show($id)
    {
        $card = $this->usersSavedCardsModel->find($id);

        if (!$card) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Card not found'], 404);
        }

        return $this->response->setJSON($card);
    }

    // Update a saved card
    public function update($id)
    {
        $data = $this->request->getRawInput();

        // Validate the input
        if (!$this->validate([
            'card_holder_first_name' => 'max_length[100]',
            'card_holder_last_name'  => 'max_length[100]',
            'billing_zip_code'       => 'max_length[10]',
            'expiration_date'        => 'regex_match[/^(0[1-9]|1[0-2])\/\d{2}$/]',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors(),
            ]);
        }

        // Update the record
        $this->usersSavedCardsModel->update($id, $data);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Card updated successfully']);
    }

    // Delete a saved card
    public function delete($id)
    {
        if (!$this->usersSavedCardsModel->delete($id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to delete card'], 500);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Card deleted successfully']);
    }
    public function setPrimary($userId, $cardId)
    {
        if ($this->usersSavedCardsModel->setPrimaryCard($userId, $cardId)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Primary card updated']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to update primary card'], 500);
    }
}
