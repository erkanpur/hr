<?php

session_start(); // Start the session at the very beginning

// Basic error reporting (for development)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Require Core files
require_once __DIR__ . '/../app/core/Database.php';
// Later we might add Autoloader.php, Router.php etc.

    $request_uri = $_SERVER['REQUEST_URI'];
    $base_path = ''; // Adjust if project is in a subdirectory, e.g., /hrms
    if (strpos(dirname($_SERVER['SCRIPT_NAME']), $base_path) !== 0 && $base_path){ // if SCRIPT_NAME already contains base_path, don't process base_path
         //This logic might need to be more robust depending on server config and if index.php is hidden
    } else if ($base_path && strpos($request_uri, $base_path) === 0) {
        $request_uri = substr($request_uri, strlen($base_path));
    }


    $request_uri = strtok($request_uri, '?'); // Remove query string
    $request_uri = trim($request_uri, '/');   // Trim leading/trailing slashes

    $segments = explode('/', $request_uri);
    $params = [];

    if (empty($segments[0])) { // Root path, e.g., "http://localhost/"
        $controllerName = 'HomeController';
        $actionName = 'index';
    } else {
        // Proposed new logic for $controllerName:
        $controllerNamePart = str_replace('_', '', ucwords(strtolower($segments[0]), '_'));
        // Attempt to singularize if it ends with 's' (basic singularization)
        if (strlen($controllerNamePart) > 1 && substr($controllerNamePart, -1) === 's') {
            // More robust singularization might be needed for irregular plurals, but this covers common cases.
            // Example: 'employees' -> 'Employee', 'leave_types' -> 'LeaveType'
            // Be careful with words naturally ending in 's' that are singular.
            // For now, this simple approach is taken.
            // A more robust check might be needed if controller names like 'StatusController' from 'status' (not 'statuses') is needed.
            // But given 'leave_types' and 'employees', this should work.
            if (strtolower(substr($controllerNamePart, -3)) === 'ies') { // e.g. categories -> category
                $controllerNamePart = substr($controllerNamePart, 0, -3) . 'y';
            } else if (strtolower(substr($controllerNamePart, -1)) === 's' && strtolower(substr($controllerNamePart, -2)) !== 'ss' ) { // general plural 's' but not 'status'
                 $controllerNamePart = substr($controllerNamePart, 0, -1);
            }
        }
        $controllerName = $controllerNamePart . 'Controller';

        if (isset($segments[1]) && !empty($segments[1])) {
            $actionName = strtolower($segments[1]);
            // Check if the action name might actually be an ID for a default action (e.g. /employees/1 should go to view(1))
            // This specific rule is for /employees/{id} -> employees/view/{id}
            if (strtolower($segments[0]) === 'employees' && is_numeric($segments[1]) && !isset($segments[2])) {
                $actionName = 'view'; // Default action for /employees/{id} is 'view'
                $params = [$segments[1]]; // The ID is the parameter
            } else {
                 // Standard controller/action/params
                 $params = array_slice($segments, 2);
            }
        } else {
            $actionName = 'index'; // Default action if only controller is specified
        }
    }
    
    // This covers employees/view/{id} explicitly if the above rule for /employees/{id} is not desired
    // or if you want both /employees/{id} and /employees/view/{id} to work.
    // If segments[0] is 'employees', segments[1] is 'view', and segments[2] is numeric (the ID)
    if (strtolower($segments[0] ?? '') === 'employees' && strtolower($segments[1] ?? '') === 'view' && isset($segments[2]) && is_numeric($segments[2])) {
        $controllerName = 'EmployeeController';
        $actionName = 'view';
        $params = [$segments[2]]; // The ID
    }


    $controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';
    ob_start();

    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        if (class_exists($controllerName)) {
            $controller = new $controllerName();
            if (method_exists($controller, $actionName)) {
                // Call the method with parameters
                call_user_func_array([$controller, $actionName], $params);
            } else {
                http_response_code(404);
                echo "<h1>404 Not Found</h1><p>Action {$actionName} not found in controller {$controllerName}.</p>";
            }
        } else {
            http_response_code(404);
            echo "<h1>404 Not Found</h1><p>Controller class {$controllerName} not found.</p>";
        }
    } else {
        // A more specific check for common assets to avoid "Controller not found" for them
        if (preg_match('/\.(?:css|js|jpg|jpeg|png|gif|ico|svg)$/i', $_SERVER['REQUEST_URI'])) {
             http_response_code(404);
             // For assets, typically the web server handles this. If PHP is hit, it means the asset wasn't found by server.
             // echo "<h1>404 Not Found</h1><p>Asset not found by PHP router.</p>"; // Usually no output for assets
        } else {
            http_response_code(404);
            echo "<h1>404 Not Found</h1><p>Controller file {$controllerFile} not found.</p>";
        }
    }

    $content = ob_get_clean();

// Load the main layout
// Variables like $title can be set in controllers and passed to the layout
// $title = 'HRMS'; // Example, should be set by controller
require_once __DIR__ . '/../templates/layout.php';

?>
