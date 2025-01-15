<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

//        $this->db = \Config\Database::connect();
//        dd($this->db->listTables());

        // Preload any models, libraries, etc, here.
        // Load constants from the database
//        $this->loadConstants();

        // E.g.: $this->session = \Config\Services::session();
    }


    /**
     * Load constants from the database and define them globally.
     *
     * @return void
     */
//    protected function loadConstants()
//    {
//        // Connect to the database
//        echo ENVIRONMENT;
//        $this->db = \Config\Database::connect();
////        dd($this->db->listTables());
////        if (!$this->db->connID) {
////            dd('Database connection failed:', $this->db->getLastQuery());
////        } else {
////            dd('Database connection successful:', $this->db);
////        }
////        dd($this->db);
//
//        // Query the 'app_constants' table
//        $builder = $this->db->table('app_constants');
//        $builder->whereIn('environment', ['all', ENVIRONMENT]); // Include 'all' and current environment
//        $query = $builder->get();
//
//        // Check if constants exist and load them
//        if ($query->getNumRows() > 0) {
//            foreach ($query->getResult() as $row) {
//                if (!defined($row->name)) {
//                    define($row->name, $row->value); // Define the constant if not already defined
//                }
//                $this->constants[$row->name] = $row->value; // Store it in the array for reference
//            }
//        }
//    }
}
