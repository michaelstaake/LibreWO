<?php
session_start();
require_once 'config.php';
require_once 'version.php';
require_once 'core/Router.php';
require_once 'core/Database.php';
require_once 'core/Controller.php';
require_once 'core/Model.php';
require_once 'core/Logger.php';

// Initialize database connection
$database = new Database();

// Initialize router
$router = new Router();

// Check if installation is needed
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = parse_url(BASE_URL, PHP_URL_PATH);
if ($basePath) {
    $currentPath = str_replace($basePath, '', $currentPath);
}
$currentPath = '/' . ltrim($currentPath, '/');

if (!$database->isInstalled() && $currentPath !== '/install') {
    // If database connection works but tables don't exist, redirect to installer
    header('Location: ' . BASE_URL . '/install');
    exit;
}

// Define routes
$router->addRoute('/', 'HomeController', 'index');
$router->addRoute('/login', 'AuthController', 'login');
$router->addRoute('/logout', 'AuthController', 'logout');
$router->addRoute('/forgot-password', 'AuthController', 'forgotPassword');
$router->addRoute('/reset-password', 'AuthController', 'resetPassword');

// Work Order routes
$router->addRoute('/work-orders', 'WorkOrderController', 'index');
$router->addRoute('/work-orders/create', 'WorkOrderController', 'create');
$router->addRoute('/work-orders/view/{id}', 'WorkOrderController', 'details');
$router->addRoute('/work-orders/print/{id}', 'WorkOrderController', 'print');
$router->addRoute('/work-orders/submitted/{id}', 'WorkOrderController', 'submitted');
$router->addRoute('/work-orders/delete/{id}', 'WorkOrderController', 'delete');

// Customer routes
$router->addRoute('/customers', 'CustomerController', 'index');
$router->addRoute('/customers/view/{id}', 'CustomerController', 'details');
$router->addRoute('/customers/merge', 'CustomerController', 'merge');
$router->addRoute('/customers/delete/{id}', 'CustomerController', 'delete');

// User routes (Admin only)
$router->addRoute('/users', 'UserController', 'index');
$router->addRoute('/users/view/{id}', 'UserController', 'details');

// Settings routes (Admin only)
$router->addRoute('/settings', 'SettingsController', 'index');

// Logs routes (Admin only)
$router->addRoute('/logs', 'LogsController', 'index');

// Install route
$router->addRoute('/install', 'InstallController', 'index');

// Error routes
$router->addRoute('/403', 'ErrorController', 'error403');
$router->addRoute('/404', 'ErrorController', 'error404');

// API routes for AJAX requests
$router->addRoute('/api/search-customers', 'ApiController', 'searchCustomers');
$router->addRoute('/api/work-order-status', 'ApiController', 'updateWorkOrderStatus');

// Process the request
$router->dispatch();
