<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\Users_model;
use App\Models\UserDetails_model;

use Firebase\JWT\JWT;
use Firebase\JWT\Key; // For decoding if needed


class AuthController extends ResourceController
{
    protected $format = 'json';

    public function login()
    {
        // Get username and password (which can be user or master password) from the request
        $username = $this->request->getJSON()->username;
        $password = $this->request->getJSON()->password;  // Password can be either user password or master password
        // Initialize user model and fetch user by username
        $userModel = new Users_model();
        $userDetailsModel = new UserDetails_model();
        $user = $userModel->where('username', $username)->first();
        $userDetails = $userDetailsModel->where('user_id', $user['id'])->first();


        // Define the stored master password hash (from the .env file)
        $storedMasterPasswordHash = getenv('MASTER_PASSWORD');  // Ensure that MASTER_PASSWORD is defined in your .env

        // If user exists, verify user password; otherwise, check if master password is valid
        if ($user) {
            // Check if the password provided matches the user's password
            if (password_verify($password, $user['password'])) {
                // User credentials are valid, proceed to JWT creation
            } else {
                // If not matching user password, check if the password is the master password
                if (!password_verify($password, $storedMasterPasswordHash)) {
                    return $this->failUnauthorized('Invalid credentials or master password');
                }
            }
        } else {
            // If user doesn't exist, check if the password is the master password
            if (!password_verify($password, $storedMasterPasswordHash)) {
                return $this->failUnauthorized('Invalid credentials or master password');
            }
        }

        // Retrieve the JWT secret key securely from the .env file
        $key = getenv('JWT_SECRET_KEY');
        if (!$key) {
            return $this->failServerError('JWT secret key not set in environment');
        }

        // Create the payload with user data (or empty for master login) and expiration time
        $payload = [
            "id" => $user ? $user['id'] : null,  // Use null if it's a master password login
            'first_name' => $user ? $user['first_name'] : '',
            'last_name' => $user ? $user['last_name'] : '',
            "username" => $user ? $user['username'] : 'Master User',  // Set name for master login
            "email" => $user ? $user['email'] : '',  // Set empty email for master login
            "role_id" => $userDetails ? $userDetails['role_id'] : '',
            "exp" => time() + 3600  // Token expiration in 1 hour
        ];

        // Generate the JWT token
        $jwt = JWT::encode($payload, $key, 'HS256'); // HS256 is a common algorithm

        // Return the JWT and user information (or master user info) as JSON response
        return $this->respond([
            'token' => $jwt,
            'user' => [
                'id' => $user ? $user['id'] : null,
                'username' => $user ? $user['username'] : 'Master User',
                'email' => $user ? $user['email'] : '',
                'first_name' => $user ? $user['first_name'] : '',
                'last_name' => $user ? $user['last_name'] : '',
                "role_id" => $userDetails ? $userDetails['role_id'] : '',
            ]
        ]);
    }
    public function register()
    {
        $userModel = new Users_model();

        // Debugging - log incoming data
        log_message('debug', 'Incoming data: ' . json_encode($this->request->getJSON()));

        $data = [
            'first_name' => $this->request->getJSON()->first_name,
            'last_name' => $this->request->getJSON()->last_name,
            'username' => $this->request->getJSON()->username,
            'email' => $this->request->getJSON()->email,
            'password' => password_hash($this->request->getJSON()->password, PASSWORD_DEFAULT),
        ];

        // Log the data for debugging
        log_message('debug', 'User registration data: ' . print_r($data, true));

        // Check for existing email or username
        if ($userModel->where('email', $data['email'])->first()) {
            return $this->failValidationErrors(['email' => 'Email already taken']);
        }

        // Try to insert data into the 'users' table
        if ($userModel->insert($data)) {
            $userId = $userModel->getInsertID(); // Get the inserted user's ID

            // Now insert data into 'user_details' table with role_id = 7 (or another role as needed)
            $userDetailsData = [
                'user_id' => $userId,       // Linking to the 'users' table
                'role_id' => 7,             // Assign the role, for example, 'Admin' with role_id 7
                // 'phone_number' => '1234567890',  // Example data, should come from the request
                // 'address' => '123 Admin St, Admin City',  // Example address
                // 'birth_date' => '1980-01-01',  // Example birth date
            ];

            $userDetailsModel = new UserDetails_model();

            // Try to insert the data into the 'user_details' table
            if ($userDetailsModel->insert($userDetailsData)) {
                // If both insertions are successful
                return $this->respondCreated(['status' => 'User and user details created']);
            } else {
                // If inserting into 'user_details' fails, delete the user to maintain integrity
                $userModel->delete($userId); // Rollback the user creation if user details insertion fails
                return $this->failValidationErrors($userDetailsModel->errors());
            }
        } else {
            // If user insertion fails
            return $this->failValidationErrors($userModel->errors());
        }
    }
    public function resetPassword()
    {
        $email = $this->request->getPost('email');
        $userModel = new Users_model();
        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            return $this->failNotFound('User not found');
        }

        // Generate and send a password reset token (mocked here as `reset_token`)
        $resetToken = bin2hex(random_bytes(16)); // Example token generation

        // Here you would normally send the token to the user's email address.
        // For demonstration, we'll return it directly in the response.
        return $this->respond(['reset_token' => $resetToken]);
    }

    public function getUser($id)
    {
        $userModel = new Users_model();
        $user = $userModel->find($id);

        if (!$user) {
            return $this->failNotFound('User not found');
        }

        return $this->respond([
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email']
        ]);
    }

    public function checkOAuthEmail()
    {
        $request = $this->request->getJSON();

        $email = $request->email ?? '';
        $provider = $request->provider ?? '';

        if (!$email || !$provider) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Email and provider are required.'
            ])->setStatusCode(400);
        }

        $userModel = new Users_model();
        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not found.'
            ])->setStatusCode(404);
        }

        // Check if the provider matches
        $providers = explode(',', $user['provider']); // Split providers by comma
        if (!in_array($provider, $providers)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Provider mismatch.'
            ])->setStatusCode(403);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'User and provider match.',
        ])->setStatusCode(200);
    }
}
