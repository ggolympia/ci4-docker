<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Database\Config as DatabaseConfig;

class DatabaseConnections extends Controller
{
    public function index()
    {
        // Define a list of database connections to test
        $connections = [
            [
                'name' => 'External MySQL 1',
                'host' => '5.161.220.216',
                'port' => 5432,
                'username' => 'mysql',
                'password' => 'MHv1JKcqbgrB0bWGJa1KLqa4Ja4KhGZl1XUlwwYvqjHnjnBLhNURpxQAMDNhD3H6',
                'database' => 'default',
                'driver' => 'MySQLi',
            ],
            [
                'name' => 'External MySQL 1',
                'host' => 'kwswscgcgokg8g800o08co0c',
                'port' => 5432,
                'username' => 'mysql',
                'password' => 'MHv1JKcqbgrB0bWGJa1KLqa4Ja4KhGZl1XUlwwYvqjHnjnBLhNURpxQAMDNhD3H6',
                'database' => 'default',
                'driver' => 'MySQLi',
            ],
            [
                'name' => 'Localhost MySQL',
                'host' => '5.161.220.216',
                'port' => 3306,
                'username' => 'mysql',
                'password' => 'MHv1JKcqbgrB0bWGJa1KLqa4Ja4KhGZl1XUlwwYvqjHnjnBLhNURpxQAMDNhD3H6',
                'database' => 'default',
                'driver' => 'MySQLi',
            ],
            // Add more connections as needed
        ];

        $results = [];

        foreach ($connections as $connection) {
            try {
                // Dynamically create a database configuration
                $dbConfig = [
                    'DSN'      => '',
                    'hostname' => $connection['host'],
                    'username' => $connection['username'],
                    'password' => $connection['password'],
                    'database' => $connection['database'],
                    'DBDriver' => $connection['driver'],
                    'port'     => $connection['port'],
                ];

                // Load the database using the configuration
                $db = DatabaseConfig::connect($dbConfig);
                $db->initialize();

                if ($db->connID) {
                    $results[] = [
                        'name' => $connection['name'],
                        'status' => 'Success',
                        'message' => 'Connection successful!',
                    ];
                } else {
                    $results[] = [
                        'name' => $connection['name'],
                        'status' => 'Failed',
                        'message' => 'Connection failed with unknown error.',
                    ];
                }

                $db->close();
            } catch (\Exception $e) {
                $results[] = [
                    'name' => $connection['name'],
                    'status' => 'Failed',
                    'message' => $e->getMessage(),
                ];
            }
        }

        // Pass the results to the view
        return view('database_connections', ['results' => $results]);
    }

    public function test()
    {
        // Your logic here
        echo 'Connection test page';
    }
}
