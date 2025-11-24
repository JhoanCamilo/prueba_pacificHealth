<?php
require_once __DIR__ . '/../models/database.php';
require_once __DIR__ . '/../models/quotation.php';


class QuotationController {
    public function search(){
        // Muestra formulario
        $view = __DIR__ . '/../Views/home.php';
        include __DIR__ . '/../Views/layout.php';
    }

    public function result(){
        $id = $_GET['id'] ?? null;
            if (empty($id) || !is_numeric($id)) {
            $error = 'Ingrese un número de cotización válido.';
            $view = __DIR__ . '/../Views/quotationinfo.php';
            include __DIR__ . '/../Views/layout.php';
            return;
        }

        $db = new Database();
        $model = new Quotation($db->pdo);

        $data = $model->findInfo((int) $id);

        $view = __DIR__ . '/../Views/quotationinfo.php';
        include __DIR__ . '/../Views/layout.php';
    }
}