<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/hello', 'Home::hello');
$routes->get('itineraries', 'ItineraryController::index');
$routes->get('test-connections', 'DatabaseConnections::index');
$routes->get('test', 'DatabaseConnections::test');
// Cruise Details View Page:
$routes->get('cruises/(:segment)/(:num)', 'ItineraryController::getCruiseByCruiselineID/$1/$2');


$routes->options('(:any)', 'Preflight::options');

// app/Config/Routes.php

$routes->group('api', ['namespace' => 'App\Controllers'], function ($routes) {
    // Users
    $routes->get('users', 'APIController::users');
    $routes->get('profiles', 'APIController::profiles');
    $routes->post('addUser', 'APIController::addUser');
    $routes->put('updateUser/(:num)', 'APIController::updateUser/$1');
    $routes->delete('deleteUser/(:num)', 'APIController::deleteUser/$1');
    $routes->get('user-details', 'APIController::userDetails');
    $routes->get('all-user-details', 'APIController::allUserDetails/$1');
    $routes->get('user-roles', 'APIController::userRoles');
    $routes->post('login', 'AuthController::login');
    $routes->post('register', 'AuthController::register');
    $routes->post('reset-password', 'AuthController::resetPassword');
    $routes->get('user/(:num)', 'AuthController::getUser/$1');
    $routes->post('check-oauth-email', 'AuthController::checkOAuthEmail');


    // Ports & Itineraries
    $routes->get('ports', 'APIController::ports');
    $routes->get('itineraries', 'APIController::itineraries');
    // Navigation
    $routes->get('navigation', 'APIController::navigation');
    $routes->get('header-nav', 'APIController::headerNav');
    $routes->get('footer-nav', 'APIController::footerNav');
    $routes->get('header/(:segment)', 'APIController::HeaderSubcatData');

    // Sliders
    $routes->get('sliders', 'APIController::homepageSlider');
    $routes->get('sliders/(:num)', 'APIController::editSlider/$1');
    $routes->put('sliders/(:num)', 'APIController::updateSlider/$1');
    $routes->post('sliders', 'APIController::addSlider');
    // Blogs
    $routes->get('blog', 'APIController::blog');
    $routes->get('blog/(:num)', 'APIController::editBlog/$1');
    $routes->put('blog/(:num)', 'APIController::updateBlog/$1');
    $routes->post('blog', 'APIController::addBlog');
    // FAQ
    $routes->get('faq', 'APIController::faq');
    $routes->get('faq/(:num)', 'APIController::editFAQ/$1');
    $routes->put('faq/(:num)', 'APIController::updateFAQ/$1');
    $routes->post('faq', 'APIController::addFAQ');
    // Files
    $routes->get('files', 'APIController::getFiles');
    $routes->post('upload', 'APIController::addUpload');
    $routes->delete('files/(:num)', 'APIController::deleteFile/$1');
    // Homepage Cruises
    $routes->get('rcg-suite', 'APIController::lowSuiteRCG');
    $routes->get('rcg-balcony', 'APIController::lowBalconyRCG');
    $routes->get('rcg-oceanview', 'APIController::lowOceanviewRCG');
    $routes->get('rcg-interior', 'APIController::lowInteriorRCG');
    // Ships
    $routes->get('ships', 'APIController::ship');
    $routes->get('ships/(:num)', 'APIController::editShip/$1');
    $routes->put('ships/(:num)', 'APIController::updateShip/$1');
    $routes->post('ships', 'APIController::addShip');
    // Ships Images
    $routes->get('ships/images', 'APIController::shipImages');
    $routes->get('ships/images/(:num)', 'APIController::editShipImages/$1');
    $routes->put('ships/images/(:num)', 'APIController::updateShipImages/$1');
    $routes->post('ships/images', 'APIController::addShipImages');
    $routes->get('images/(:any)', 'ImageController::fetchImage/$1');
    // Amenities
    $routes->get('ships/amenities', 'APIController::amenities');
    $routes->get('ships/amenities/(:num)', 'APIController::editShipAmenity/$1');
    $routes->put('ships/amenities/(:num)', 'APIController::updateShipAmenity/$1');
    $routes->post('ships/amenities', 'APIController::addShipAmenity');
    // Dining
    $routes->get('ships/dining', 'APIController::dining');
    $routes->get('ships/dining/(:num)', 'APIController::editShipDining/$1');
    $routes->put('ships/dining/(:num)', 'APIController::updateShipDining/$1');
    $routes->post('ships/dining', 'APIController::addShipDining');
    // Lounge
    $routes->get('ships/lounges', 'APIController::lounges');
    $routes->get('ships/lounges/(:num)', 'APIController::editShipLounge/$1');
    $routes->put('ships/lounges/(:num)', 'APIController::updateShipLounge/$1');
    $routes->post('ships/lounges', 'APIController::addShipLounge');
    // Cruises Market
    $routes->get('market', 'MarketController::index');  // For GET request to fetch filtered items
    // Passenger
    $routes->get('passengers', 'PassengersController::getPassengers');
    $routes->get('edit-passenger/(:num)', 'PassengersController::get/$1');  // Get passenger by ID
    $routes->post('create-passenger', 'PassengersController::create');  // Create a new passenger
    $routes->put('edit-passenger/(:num)', 'PassengersController::update/$1');  // Update passenger by ID
    $routes->put('deactivate-passenger/(:num)', 'PassengersController::deactivate/$1');  // Mark passenger as inactive
    // Saved Credit Cards
    $routes->post('card/create', 'UsersSavedCardsController::create'); // Create a new card
    $routes->get('cards/(:num)', 'UsersSavedCardsController::index/$1'); // List all cards for a user
    $routes->get('card/(:num)', 'UsersSavedCardsController::show/$1'); // Show a specific card
    $routes->put('card/update/(:num)', 'UsersSavedCardsController::update/$1'); // Update a card
    $routes->put('card/set-primary/(:segment)/(:segment)', 'UsersSavedCardsController::setPrimary/$1/$2');
    $routes->delete('card/delete/(:num)', 'UsersSavedCardsController::delete/$1'); // Delete a card
    // Stripe
    $routes->post('create-payment-intent', 'StripeController::createPaymentIntent');
    // Config
    $routes->get('get-configurations', 'ConfigController::getConfigurations');
    $routes->post('update-config', 'ConfigController::updateConfigurations');
    $routes->post('deactivate-config', 'ConfigController::deactivateConfigurations');
    $routes->get('config/(:segment)', 'ConfigController::getConfigurationByKey/$1');
    // Maintenance
    $routes->post('maintenance/set-access-cookie', 'MaintenanceController::setAccessCookie');
    $routes->get('maintenance/validate-access', 'MaintenanceController::validateAccess');
    // Sessions
    $routes->post('user-sessions', 'UserSessionsController::store');
    $routes->get('user-sessions/(:num)', 'UserSessionsController::getSessionsByID/$1');
});