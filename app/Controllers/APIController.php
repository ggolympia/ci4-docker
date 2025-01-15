<?php

namespace App\Controllers;

use App\Models\Content_model;
use App\Models\Itinerary_model;
use App\Models\Users_model;
use App\Models\Navigation_model;
use App\Models\Ship_model;
use App\Models\ShipImages_model;
use App\Models\ShipLounges_model;
use App\Models\Ports_model;
use App\Models\Cruiselines_model;
use App\Models\DestinationsModel;
use CodeIgniter\RESTful\ResourceController;

class APIController extends ResourceController
{
    // The base path is /api
    protected $format = 'json'; // Set response format to JSON by default
    protected $itineraryModel;
    protected $navigationModel;

    // Constructor to initialize all required models
    public function __construct()
    {
        $this->itineraryModel = new Itinerary_model();
        $this->navigationModel = new Navigation_model();
    }

    /**
     * @api GET /api/users
     */
    public function users()
    {
        // Example API call to fetch users
        $usersModel = new \App\Models\Users_model();
        $users = $usersModel->getUsers();

        return $this->respond($users); // Return the list of users as a JSON response
    }

    /**
     * @api GET /api/profiles
     */
    public function profiles()
    {
        // Example API call to fetch profiles
        $usersModel = new \App\Models\Users_model();
        $profiles = $usersModel->getProfiles();

        return $this->respond($profiles); // Return profiles as JSON
    }

    /**
     * @api POST /api/addUser
     */
    public function addUser()
    {
        // Handle user addition (e.g., through a POST request)
        $data = $this->request->getPost();

        // Assume some validation and database operation here
        // Example:
        $usersModel = new \App\Models\Users_model();
        $usersModel->insert($data); // Assuming this uses CI4's built-in insert method

        return $this->respondCreated(['message' => 'User created successfully']);
    }

    /**
     * @api PUT /api/updateUser/(:num)
     */
    public function updateUser($id)
    {
        // Handle user update (e.g., through a PUT request)
        $data = $this->request->getRawInput(); // Get data from PUT request

        $usersModel = new \App\Models\Users_model();
        $usersModel->update($id, $data); // Update user with new data

        return $this->respond(['message' => 'User updated successfully']);
    }

    /**
     * @api DELETE /api/deleteUser/(:num)
     */
    public function deleteUser($id)
    {
        // Handle user deletion
        $usersModel = new \App\Models\Users_model();
        $usersModel->delete($id); // Delete the user by ID

        return $this->respondDeleted(['message' => 'User deleted successfully']);
    }
    public function userDetails()
    {
        // Example API call to fetch users
        $usersModel = new \App\Models\Users_model();
        $users = $usersModel->getUserDetails();

        return $this->respond($users); // Return the list of users as a JSON response
    }
    public function allUserDetails($userID)
    {
        // Example API call to fetch users
        $userID = $this->request->getGet('userID'); // Get the userID from the query string

        if ($userID) {

            $usersModel = new \App\Models\Users_model();
            $users = $usersModel->getAllUserDetails($userID);
            // return $this->response->setJSON([$users]);

            return $this->respond($users); // Return the list of users as a JSON response
        }
        return $this->response->setStatusCode(400, 'User ID is required');
    }
    public function userRoles()
    {
        // Example API call to fetch users
        $usersModel = new \App\Models\Users_model();
        $roles = $usersModel->getUserRoles();

        return $this->respond($roles);
    }

    // Homeport
    public function ports()
    {
        // Example API call to fetch users
        $itineraryModel = new \App\Models\Itinerary_model();
        $ports = $itineraryModel->getPorts();

        return $this->respond($ports);
    }
    public function itineraries()
    {
        // Example API call to fetch users
        $itineraryModel = new \App\Models\Itinerary_model();
        $itineraries = $itineraryModel->getItineraries();

        return $this->respond($itineraries);
    }
    public function navigation()
    {
        // Example API call to fetch users
        $navModel = new \App\Models\Navigation_model();
        $navigation = $navModel->getNavigation();

        return $this->respond($navigation); // Return the list of users as a JSON response
    }
    // Navigation
    public function headerNav()
    {
        // Example API call to fetch users
        $navigationModel = new \App\Models\Navigation_model();
        $headerNav = $navigationModel->getHeaderNavItems();

        return $this->respond($headerNav);
    }
    public function footerNav()
    {
        // Example API call to fetch users
        $navigationModel = new \App\Models\Navigation_model();
        $footerNav = $navigationModel->getFooterNavItems();

        return $this->respond($footerNav);
    }

    // Sliders
    public function homepageSlider()
    {
        // Example API call to fetch homepage slider items
        $contentModel = new \App\Models\Content_model();
        $homepageSliders = $contentModel->getHomepageSliders();

        return $this->respond($homepageSliders);
    }
    public function editSlider($id)
    {
        $contentModel = new \App\Models\Content_model();
        $sliderData = $contentModel->getSliderById($id);
        return $this->respond($sliderData);
    }
    public function updateSlider($id)
    {
        // Log the incoming request data
        log_message('info', 'Request Data: ' . print_r($this->request->getBody(), true));

        // Get JSON data from the request
        $data = $this->request->getJSON();

        // Create data array
        $dataArray = [
            'slider_name' => $data->slider_name ?? null,
            'slider_title' => $data->slider_title ?? null,
            'slider_desc' => $data->slider_desc ?? null,
            'status' => $data->status ?? null,
        ];

        // Call the model's update method
        $contentModel = new \App\Models\Content_model();
        if ($contentModel->updateSliderById($id, $dataArray)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Slider updated successfully.']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to update slider.'], 400);
        }
    }
    public function addSlider()
    {
        $contentModel = new Content_model();

        $data = [
            'slider_name' => $this->request->getPost('slider_name'),
            'slider_title' => $this->request->getPost('slider_title'),
            'slider_desc' => $this->request->getPost('slider_desc'),
            'status' => $this->request->getPost('status'),
            'alt_text' => $this->request->getPost('alt_text'),
        ];

        $file = $this->request->getFile('slider_image');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Get the original filename and extension
            $originalName = $file->getClientName();
            $extension = $file->getExtension();

            // Set the upload directory
            $uploadDir = FCPATH . 'images/slider/';

            // Check and create the directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Prepare the new filename
            $newFileName = $originalName;
            $fileCounter = 1;

            // Check if the file already exists and generate a new name if it does
            while (file_exists($uploadDir . $newFileName)) {
                $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                $newFileName = $nameWithoutExt . '_' . $fileCounter . '.' . $extension;
                $fileCounter++;
            }

            if ($file->move($uploadDir, $newFileName)) {
                $data['file_name'] = $newFileName;
                $data['file_size'] = $file->getSize();
                $data['file_type'] = $file->getClientMimeType();
                $data['link_url'] = base_url('images/slider/' . $newFileName);
            } else {
                log_message('error', 'File move failed: ' . $file->getErrorString() . ' to ' . $uploadDir);
                return $this->fail('File upload failed: ' . $file->getErrorString());
            }
        } else {
            log_message('error', 'File upload failed: ' . ($file ? $file->getErrorString() : 'No file uploaded'));
            return $this->fail('File upload failed: ' . ($file ? $file->getErrorString() : 'No file uploaded'));
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'slider_name'  => 'required|max_length[255]',
            'slider_title' => 'required|max_length[255]',
            'slider_desc'  => 'required',
            'status'       => 'required|in_list[0,1]',
        ]);

        if (!$validation->run($data)) {
            return $this->failValidationErrors($validation->getErrors());
        }

        $sliderId = $contentModel->addSlider($data);

        if ($sliderId) {
            return $this->respondCreated([
                'message'   => 'Slider added successfully',
                'slider_id' => $sliderId
            ]);
        } else {
            return $this->failServerError('Failed to add slider');
        }
    }
    // Blog
    public function blog()
    {
        // Example API call to fetch blogs
        $blogModel = new \App\Models\Blog_model();
        $blogs = $blogModel->getBlogs();

        return $this->respond($blogs);
    }
    /**
     * @api PUT /api/updateBlog/(:num)
     */
    public function updateBlog($id)
    {
        // Get JSON data from the request
        $data = $this->request->getJSON();

        // Create data array
        $dataArray = [
            'blog_name' => $data->blog_name ?? null,
            'blog_title' => $data->blog_title ?? null,
            'blog_desc' => $data->blog_desc ?? null,
            'blog_body' => $data->blog_body ?? null,
            'blog_image' => $data->blog_image ?? null,
            'author_user_id' => $data->author_user_id ?? null,
            'status' => $data->status ?? null,
            'updated_date' => date('Y-m-d H:i:s') // Generate current timestamp
        ];

        // Call the model's update method
        $blogModel = new \App\Models\Blog_model();
        if ($blogModel->updateBlogById($id, $dataArray)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Slider updated successfully.']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to update slider.'], 400);
        }
    }

    public function editBlog($id)
    {
        $blogModel = new \App\Models\Blog_model();
        $blogData = $blogModel->getBlogById($id);
        return $this->respond($blogData);
    }

    /**
     * @api DELETE /api/deleteBlog/(:num)
     */
    public function deleteBlog($id)
    {
        // Handle user deletion
        $blogModel = new \App\Models\Blog_model();
        $blogModel->delete($id); // Delete the user by ID

        return $this->respondDeleted(['message' => 'Blog Entry deleted successfully']);
    }
    public function faq()
    {
        // Example API call to fetch users
        $contentModel = new \App\Models\Content_model();
        $faq = $contentModel->getFAQ();

        return $this->respond($faq); // Return the list of users as a JSON response
    }
    public function addUpload()
    {
        // Load the model
        $uploadModel = new \App\Models\Upload_model();

        // Get the incoming request data
        $data = [
            'file_name' => $this->request->getPost('file_name')
        ];

        // Handle file upload
        $file = $this->request->getFile('file');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Get the original filename and extension
            $originalName = $file->getClientName();
            $extension = $file->getExtension();

            // Set the upload directory to public/uploads/images
            $uploadDir = FCPATH . 'uploads/images/'; // FCPATH points to the public directory

            // Check and create the directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Prepare the new filename
            $newFileName = $originalName;
            $fileCounter = 1;

            // Check if the file already exists and generate a new name if it does
            while (file_exists($uploadDir . $newFileName)) {
                $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                $newFileName = $nameWithoutExt . '_' . $fileCounter . '.' . $extension;
                $fileCounter++;
            }

            // Move the file to the designated path
            if (!$file->move($uploadDir, $newFileName)) {
                log_message('error', 'File move failed: ' . $file->getErrorString());
                return $this->fail('File upload failed: ' . $file->getErrorString());
            }

            // Add file details to the data array
            $data['file_name'] = $newFileName;
            $data['file_size'] = $file->getSize();  // Size in bytes
            $data['file_type'] = $file->getClientMimeType();  // MIME type

            // Assign the accessible link URL
            $data['link_url'] = base_url('uploads/images/' . $newFileName);
        } else {
            return $this->fail('File upload failed.');
        }

        // Call the model method to add the new file details
        $fileId = $uploadModel->addFiles($data);

        // Check if the file was added successfully
        if ($fileId) {
            return $this->respondCreated([
                'message' => 'File added successfully',
                'file_id' => $fileId
            ]);
        } else {
            return $this->failServerError('Failed to add File');
        }
    }

    public function getFiles()
    {
        $uploadModel = new \App\Models\Upload_model();
        $files = $uploadModel->getFiles();

        return $this->respond($files); // Return the list of users as a JSON response
    }
    public function deleteFile($id)
    {
        $uploadModel = new \App\Models\Upload_model();
        $uploadModel->delete($id);

        return redirect()->back()->with('message', 'File deleted successfully!');
    }
    /**
     * @api GET /api/rcg-suite
     */
    public function lowSuiteRCG()
    {
        $rcg_suite = $this->itineraryModel->getLowSuiteRCG();
        return $this->respond($rcg_suite);
    }
    /**
     * @api GET /api/rcg-balcony
     */
    public function lowBalconyRCG()
    {
        $rcg_balcony = $this->itineraryModel->getLowBalconyRCG();
        return $this->respond($rcg_balcony);
    }
    /**
     * @api GET /api/rcg-oceanview
     */
    public function lowOceanviewRCG()
    {
        $rcg_oceanview = $this->itineraryModel->getLowOceanviewRCG();
        return $this->respond($rcg_oceanview);
    }
    /**
     * @api GET /api/rcg-interior
     */
    public function lowInteriorRCG()
    {
        $rcg_interior = $this->itineraryModel->getLowOceanviewRCG();
        return $this->respond($rcg_interior);
    }
    public function HeaderSubcatData()
    {
        $menuTitle = $this->request->getVar('category');
        $subcategory = $this->request->getVar('subcategory');

        if (!$menuTitle || !$subcategory) {
            return $this->failValidationError('Both category and subcategory are required.');
        }

        $data = $this->navigationModel->getSubcatbyMenuTitle($menuTitle, $subcategory);

        if (empty($data)) {
            return $this->failNotFound('No data found for the given category and subcategory.');
        }

        return $this->respond($data);
    }
    // Ships
    public function ship()
    {
        // Example API call to fetch homepage slider items
        $shipModel = new \App\Models\Ship_model();
        $ships = $shipModel->getShips();

        return $this->respond($ships);
    }
    public function editShip($id)
    {
        $shipModel = new \App\Models\Ship_model();
        $shipData = $shipModel->getShipsById($id);
        return $this->respond($shipData);
    }
    public function updateShip($id)
    {
        // Log the incoming request data
        log_message('info', 'Request Data: ' . print_r($this->request->getBody(), true));

        // Get JSON data from the request
        $data = $this->request->getJSON();

        // Create data array
        $dataArray = [
            'cruiseline' => $data->cruiseline ?? null,
            'name' => $data->name ?? null,
            'maiden_voyage' => $data->maiden_voyage ?? null,
            'built_at' => $data->built_at ?? null,
            'total_decks' => $data->total_decks ?? null,
            'guest_decks' => $data->guest_decks ?? null,
            'crew_count' => $data->crew_count ?? null,
            'staterooms' => $data->staterooms ?? null,
            'guest_capacity' => $data->guest_capacity ?? null,
            'double_occupancy_capacity' => $data->double_occupancy_capacity ?? null,
            'gross_tonnage' => $data->gross_tonnage ?? null,
            'length_ft' => $data->length_ft ?? null,
            'width_ft' => $data->width_ft ?? null,
            'draft_ft' => $data->draft_ft ?? null,
            'status' => $data->status ?? null,
        ];

        // Call the model's update method
        $shipModel = new \App\Models\Ship_model();
        if ($shipModel->updateShipsById($id, $dataArray)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Slider updated successfully.']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to update slider.'], 400);
        }
    }
    public function addShip()
    {
        $shipModel = new Ship_model();

        $data = [
            'cruiseline' => $this->request->getPost('cruiseline'),
            'name' => $this->request->getPost('ship_name'),
            'maiden_voyage' => $this->request->getPost('maiden_voyage'),
            'built_at' => $this->request->getPost('built_at'),
            'total_decks' => $this->request->getPost('total_decks'),
            'guest_decks' => $this->request->getPost('guest_decks'),
            'crew_count' => $this->request->getPost('crew_count'),
            'staterooms' => $this->request->getPost('staterooms'),
            'guest_capacity' => $this->request->getPost('guest_capacity'),
            'double_occupancy_capacity' => $this->request->getPost('double_occupancy_capacity'),
            'gross_tonnage' => $this->request->getPost('gross_tonnage'),
            'length_ft' => $this->request->getPost('length_ft'),
            'width_ft' => $this->request->getPost('width_ft'),
            'draft_ft' => $this->request->getPost('draft_ft'),
            'status' => $this->request->getPost('status'),

        ];

        // $validation = \Config\Services::validation();
        // $validation->setRules([
        //     'slider_name'  => 'required|max_length[255]',
        //     'slider_title' => 'required|max_length[255]',
        //     'slider_desc'  => 'required',
        //     'status'       => 'required|in_list[0,1]',
        // ]);

        // if (!$validation->run($data)) {
        //     return $this->failValidationErrors($validation->getErrors());
        // }

        $shipId = $shipModel->addShip($data);

        if ($shipId) {
            return $this->respondCreated([
                'message'   => 'Ship added successfully',
                'ship_id' => $shipId
            ]);
        } else {
            return $this->failServerError('Failed to add ship');
        }
    }
    // Ship Images
    public function shipImages()
    {
        // Example API call to fetch homepage slider items
        $shipImagesModel = new \App\Models\ShipImages_model();
        $images = $shipImagesModel->getShipImages();

        return $this->respond($images);
    }
    public function editShipImages($id)
    {
        $shipImagesModel = new \App\Models\ShipImages_model();
        $shipImagesData = $shipImagesModel->getShipImagesById($id);
        return $this->respond($shipImagesData);
    }
    /**
     * @api PUT /api/updateBlog/(:num)
     */
    public function updateShipImages($id)
    {
        // Get the raw input
        $data = $this->request->getJSON(true);

        // Log the input to check its format
        // log_message('info', 'Raw input data: ' . json_encode($data));

        $shipImagesModel = new ShipImages_model();

        // Check if data is valid and contains allowed fields
        if ($shipImagesModel->updateShipImagesById($id, $data)) {
            return $this->respondUpdated(['message' => 'Ship updated successfully']);
        } else {
            return $this->fail('Failed to update blog');
        }
    }
    public function addShipImages()
    {
        $shipImagesModel = new ShipImages_model();
        $uploadModel = new \App\Models\Upload_model();

        $shipName = [
            'ship_name' => $this->request->getPost('ship_name'),

        ];

        // Retrieve POST data related to the ship image
        $shipImageData = [
            'ship_id' => $this->request->getPost('ship_id'),
            'category' => $this->request->getPost('category'),
            'description' => $this->request->getPost('description'),
            'destination_route' => $this->request->getPost('destination_route'),
            'uploaded_at' => date('Y-m-d H:i:s'), // Capture the current timestamp
            'photo_credit' => $this->request->getPost('photo_credit'),
            'status' => $this->request->getPost('status'),
        ];

        // Handle the file upload
        $file = $this->request->getFile('ship_image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Extract original filename and extension
            $originalName = $file->getClientName();
            $extension = $file->getExtension();

            // Set upload directory with ship name
            $uploadDir = FCPATH . 'images/ships/' . $shipName['ship_name'] . '/';

            // Create the directory if it does not exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generate a unique filename if the file already exists
            $newFileName = $originalName;
            $fileCounter = 1;
            while (file_exists($uploadDir . $newFileName)) {
                $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                $newFileName = $nameWithoutExt . '_' . $fileCounter . '.' . $extension;
                $fileCounter++;
            }

            // Move the uploaded file
            if ($file->move($uploadDir, $newFileName)) {
                // Prepare upload data
                $uploadData = [
                    'file_name' => $newFileName,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'created_date' => date('Y-m-d H:i:s'),
                    'updated_date' => date('Y-m-d H:i:s'),
                    'link_url' => base_url('images/ships/' . $shipName['ship_name'] . '/' . $newFileName),
                ];

                // Insert upload data into the Uploads model
                $fileId = $uploadModel->addFiles($uploadData);

                // Link the uploaded file to the ship image record
                $shipImageData['file_id'] = $fileId;
                $shipImageData['image_name'] = $newFileName;
                /** @var string */
                $shipImageData['image_url'] = 'images/ships/' . $shipName['ship_name'] . '/' . $newFileName;
            } else {
                log_message('error', 'File move failed: ' . $file->getErrorString() . ' to ' . $uploadDir);
                return $this->fail('File upload failed: ' . $file->getErrorString());
            }
        } else {
            log_message('error', 'File upload failed: ' . ($file ? $file->getErrorString() : 'No file uploaded'));
            return $this->fail('File upload failed: ' . ($file ? $file->getErrorString() : 'No file uploaded'));
        }

        // Insert ship image data into the ShipImages model
        $shipId = $shipImagesModel->addShipImages($shipImageData);

        if ($shipId) {
            return $this->respondCreated([
                'message' => 'Ship Image added successfully',
                'id' => $shipId,
                'data' => $shipImageData
            ]);
        } else {
            return $this->failServerError('Failed to add ship image');
        }
    }
    // Ships
    public function amenities()
    {
        // Example API call to fetch homepage slider items
        $shipAmenity = new \App\Models\ShipAmenities_model();
        $ships = $shipAmenity->getAmenities();

        return $this->respond($ships);
    }
    public function editShipAmenity($id)
    {
        $shipAmenity = new \App\Models\ShipAmenities_model();
        $shipAmenityData = $shipAmenity->getAmenityById($id);
        return $this->respond($shipAmenityData);
    }
    public function updateShipAmenity($id)
    {
        // Log the incoming request data
        log_message('info', 'Request Data: ' . print_r($this->request->getBody(), true));

        // Get JSON data from the request
        $data = $this->request->getJSON();

        // Create data array
        $dataArray = [
            'name' => $data->name ?? null,
            'ship_id' => $data->ship_id ?? null,
            'category' => $data->category ?? null,
            'status' => $data->status ?? null,
            'created_date' => $data->created_date ?? null,
            'updated_date' => $data->updated_date ?? null,
        ];

        // Call the model's update method
        $shipAmenity = new \App\Models\ShipAmenities_model();
        if ($shipAmenity->updateAmenityById($id, $dataArray)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Slider updated successfully.']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to update slider.'], 400);
        }
    }
    public function addShipAmenity()
    {
        $shipAmenity = new \App\Models\ShipAmenities_model();

        $data = [
            'category' => $this->request->getPost('category'),
            'cruiseline' => $this->request->getPost('cruiseline'),
            'name' => $this->request->getPost('name'),
            'ship_id' => $this->request->getPost('ship_id'),
            'ship_name' => $this->request->getPost('ship_name'),
            'status' => $this->request->getPost('status'),
        ];

        // $validation = \Config\Services::validation();
        // $validation->setRules([
        //     'slider_name'  => 'required|max_length[255]',
        //     'slider_title' => 'required|max_length[255]',
        //     'slider_desc'  => 'required',
        //     'status'       => 'required|in_list[0,1]',
        // ]);

        // if (!$validation->run($data)) {
        //     return $this->failValidationErrors($validation->getErrors());
        // }

        $shipAmenityId = $shipAmenity->addShipAmenity($data);

        if ($shipAmenityId) {
            return $this->respondCreated([
                'message'   => 'Ship amenity added successfully',
                'ship_id' => $shipAmenityId
            ]);
        } else {
            return $this->failServerError('Failed to add ship');
        }
    }
    // Ships Lounges
    public function lounges()
    {
        // Example API call to fetch homepage slider items
        $shipLounge = new \App\Models\ShipLounges_model();
        $lounges = $shipLounge->getLounges();

        return $this->respond($lounges);
    }
    public function editShipLounge($id)
    {
        $shipLounge = new \App\Models\ShipLounges_model();
        $shipLoungeData = $shipLounge->getLoungeById($id);
        return $this->respond($shipLoungeData);
    }
    public function updateShipLounge($id)
    {
        // Log the incoming request data
        log_message('info', 'Request Data: ' . print_r($this->request->getBody(), true));

        // Get JSON data from the request
        $data = $this->request->getJSON();

        // Create data array
        $dataArray = [
            'cruiseline' => $data->cruiseline ?? null,
            'name' => $data->name ?? null,
            'maiden_voyage' => $data->maiden_voyage ?? null,
            'built_at' => $data->built_at ?? null,
            'total_decks' => $data->total_decks ?? null,
            'guest_decks' => $data->guest_decks ?? null,
            'crew_count' => $data->crew_count ?? null,
            'staterooms' => $data->staterooms ?? null,
            'guest_capacity' => $data->guest_capacity ?? null,
            'double_occupancy_capacity' => $data->double_occupancy_capacity ?? null,
            'gross_tonnage' => $data->gross_tonnage ?? null,
            'length_ft' => $data->length_ft ?? null,
            'width_ft' => $data->width_ft ?? null,
            'draft_ft' => $data->draft_ft ?? null,
            'status' => $data->status ?? null,
        ];

        // Call the model's update method
        $shipLounge = new \App\Models\ShipLounges_model();
        if ($shipLounge->updateLoungeById($id, $dataArray)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Slider updated successfully.']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to update slider.'], 400);
        }
    }
    public function addShipLounge()
    {
        $shipLounge = new \App\Models\ShipLounges_model();

        $data = [
            'cruiseline' => $this->request->getPost('cruiseline'),
            'name' => $this->request->getPost('ship_name'),
            'maiden_voyage' => $this->request->getPost('maiden_voyage'),
            'built_at' => $this->request->getPost('built_at'),
            'total_decks' => $this->request->getPost('total_decks'),
            'guest_decks' => $this->request->getPost('guest_decks'),
            'crew_count' => $this->request->getPost('crew_count'),
            'staterooms' => $this->request->getPost('staterooms'),
            'guest_capacity' => $this->request->getPost('guest_capacity'),
            'double_occupancy_capacity' => $this->request->getPost('double_occupancy_capacity'),
            'gross_tonnage' => $this->request->getPost('gross_tonnage'),
            'length_ft' => $this->request->getPost('length_ft'),
            'width_ft' => $this->request->getPost('width_ft'),
            'draft_ft' => $this->request->getPost('draft_ft'),
            'status' => $this->request->getPost('status'),

        ];

        // $validation = \Config\Services::validation();
        // $validation->setRules([
        //     'slider_name'  => 'required|max_length[255]',
        //     'slider_title' => 'required|max_length[255]',
        //     'slider_desc'  => 'required',
        //     'status'       => 'required|in_list[0,1]',
        // ]);

        // if (!$validation->run($data)) {
        //     return $this->failValidationErrors($validation->getErrors());
        // }

        $shipLoungeId = $shipLounge->addLounge($data);

        if ($shipLoungeId) {
            return $this->respondCreated([
                'message'   => 'Ship added successfully',
                'ship_id' => $shipLoungeId
            ]);
        } else {
            return $this->failServerError('Failed to add ship');
        }
    }
    // Ships Dining
    public function dining()
    {
        // Example API call to fetch homepage slider items
        $shipDining = new \App\Models\ShipDining_model();
        $ships = $shipDining->getDining();

        return $this->respond($ships);
    }
    public function editShipDining($id)
    {
        $shipDining = new \App\Models\ShipDining_model();
        $shipDiningData = $shipDining->getDiningById($id);
        return $this->respond($shipDiningData);
    }
    public function updateShipDining($id)
    {
        // Log the incoming request data
        log_message('info', 'Request Data: ' . print_r($this->request->getBody(), true));

        // Get JSON data from the request
        $data = $this->request->getJSON();

        // Create data array
        $dataArray = [
            'ship_id' => $data->ship_id ?? null,
            'dining_name' => $data->dining_name ?? null,
            'price_range' => $data->price_range ?? null,
            'cuisine' => $data->cuisine ?? null,
            'status' => $data->status ?? null,
            'image_id' => $data->image_id ?? null,
            'updated_date' => $data->updated_date ?? null,
        ];

        // Call the model's update method
        $shipDining = new \App\Models\ShipDining_model();
        if ($shipDining->updateDiningById($id, $dataArray)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Slider updated successfully.']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to update slider.'], 400);
        }
    }
    public function addShipDining()
    {
        $shipDining = new \App\Models\ShipDining_model();

        $data = [
            'ship_id' => $this->request->getPost('ship_id'),
            'ship_name' => $this->request->getPost('ship_name'),
            'dining_name' => $this->request->getPost('dining_name'),
            'cuisine' => $this->request->getPost('cuisine'),
            'price_range' => $this->request->getPost('price_range'),
            'image_id' => $this->request->getPost('image_id'),
            'status' => $this->request->getPost('status'),

        ];

        // $validation = \Config\Services::validation();
        // $validation->setRules([
        //     'slider_name'  => 'required|max_length[255]',
        //     'slider_title' => 'required|max_length[255]',
        //     'slider_desc'  => 'required',
        //     'status'       => 'required|in_list[0,1]',
        // ]);

        // if (!$validation->run($data)) {
        //     return $this->failValidationErrors($validation->getErrors());
        // }

        $shipDiningId = $shipDining->addShipDining($data);

        if ($shipDiningId) {
            return $this->respondCreated([
                'message'   => 'dining added successfully',
                'ship_id' => $shipDiningId
            ]);
        } else {
            return $this->failServerError('Failed to add dining');
        }
    }
}
