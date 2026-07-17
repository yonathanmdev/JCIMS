<?php
namespace App\Models;

use PDO;

class ProjectNgoModel {
    private $db;

    /**
     * ኮኔክሽኑን ከውጭ ይቀበላል (Dependency Injection)
     * ይህ በየቦታው አዲስ ኮኔክሽን እንዳይከፈት ይረዳል
     */
    public function __construct($db) {
        $this->db = $db;
    }

public function getAllProjectNgos() {
    $query = "SELECT pid, pname FROM projectngos ORDER BY pname ASC";
    $stmt = $this->db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


}