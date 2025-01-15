<?php

namespace App\Controllers;

use App\Models\Config_model;
use App\Models\Passenger_model;
use CodeIgniter\RESTful\ResourceController;

class ConfigController extends ResourceController
{
    protected $modelName = 'App\Models\Passenger_model';
    protected $format = 'json';

// In app/Controllers/ConfigController.php
    public function index()
    {
        $configModel = new Config_model();

        // Fetch all configurations
        $configurations = $configModel->findAll();

        // Check if no configurations are found
        if ($configurations === null) {
            $configurations = []; // Return an empty array if no data
        }

        // Now you can safely use foreach
        foreach ($configurations as $config) {
            // Process the configuration
        }
    }

    public function getConfigurations()
    {
        $configModel = new Config_model();

        // Fetch all configurations where status = 1
        $configs = $configModel->where('status', 1)->findAll();

        return $this->response->setJSON($configs);
    }


    public function updateConfigurations()
    {
        $payload = $this->request->getJSON(true); // Parse JSON as an associative array
        $configurations = $payload['configurations'] ?? null;

        if (empty($configurations)) {
            return $this->response->setJSON(['error' => 'No configurations provided']);
        }

        $configModel = new Config_model();

        // Debugging: Log the incoming configurations
//        log_message('error', 'Configurations: ' . print_r($configurations, true));

        foreach ($configurations as $config) {
            // Debugging: Log each individual config item
//            log_message('error', 'Config Item: ' . print_r($config, true));

            if (empty($config['config_key']) || empty($config['config_value'])) {
                return $this->response->setJSON(['error' => 'Each configuration must have config_key and config_value']);
            }

            if (!empty($config)) {
                log_message('debug', 'Processing Config: ' . print_r($config, true));

                if (!empty($config['id'])) {
                    // Check if a configuration with the provided ID exists
                    $existingConfig = $configModel->find($config['id']);
                    if ($existingConfig) {
                        // Update the existing configuration
                        $configModel->update($config['id'], [
                            'config_key' => $config['config_key'],
                            'config_value' => $config['config_value'],
                            'description' => $config['description'] ?? '',
                        ]);
                        log_message('info', 'Updated Config ID: ' . $config['id']);
                    }
                } else {
                    // Insert a new configuration
                    $data = [
                        'config_key' => $config['config_key'],
                        'config_value' => $config['config_value'],
                        'description' => $config['description'] ?? '',
                    ];

                    log_message('error', 'Insert Data: ' . print_r($data, true));

                    if ($configModel->insert($data) === false) {
                        // Log validation errors if insertion fails
                        log_message('error', 'Insert Errors: ' . print_r($configModel->errors(), true));
                    } else {
                        log_message('info', 'Inserted New Config: ' . $config['config_key']);
                    }
                }
            }


        }
        return $this->response->setJSON(['success' => 'Configurations updated successfully']);

    }

    public function deactivateConfigurations()
    {
        // Get the configuration ID to deactivate from the request
        $payload = $this->request->getJSON(true); // Parse JSON as an associative array
        $configId = $payload['config_id'] ?? null;
        log_message('info', 'Config ID: ' . $configId);

        // Validate the input
        if (empty($configId)) {
            return $this->response->setJSON(['error' => 'Invalid or missing configuration ID']);
        }

        // Instantiate the Config_model
        $configModel = new Config_model();

        // Check if the configuration exists in the database
        $existingConfig = $configModel->find($configId);
        if (!$existingConfig) {
            return $this->response->setJSON(['error' => 'Configuration not found']);
        }

        // Deactivate the configuration by calling the model's method
        $updated = $configModel->deactivateConfiguration($configId);

        // Check if the update was successful
        if ($updated) {
            return $this->response->setJSON(['success' => 'Configuration deactivated successfully']);
        } else {
            return $this->response->setJSON(['error' => 'Failed to deactivate configuration']);
        }
    }
    public function getConfigurationByKey($key)
    {
        // Use the Config_model to fetch the configuration based on the config_key
        $configModel = new Config_model();

        // Get the configuration by key
        $config = $configModel->where('config_key', $key)->first();

        // Check if the configuration was found
        if ($config) {
            return $this->response->setJSON($config);
        } else {
            return $this->response->setJSON(['error' => 'Configuration not found']);
        }
    }







}
