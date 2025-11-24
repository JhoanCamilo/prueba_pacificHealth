<?php
require_once __DIR__ . '/../app/controllers/quotationController.php';


$action = $_GET['action'] ?? 'search';
$controller = new QuotationController();


switch ($action) {
    case 'search':
        $controller->search();
    break;
    case 'result':
        $controller->result();
    break;
    default:
        http_response_code(404);
        echo "Acción no encontrada";
}