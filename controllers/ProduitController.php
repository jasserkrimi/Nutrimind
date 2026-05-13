<?php
/**
 * ProduitController.php
 * Gestion de la boutique sport — Back-Office NutriMind
 */
class ProduitController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function index() {
        $db = $this->db; // rendre $db accessible dans la vue
        require_once 'views/produits/index.php';
    }
}
?>
