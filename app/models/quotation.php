<?php
class Quotation {
    private $pdo;


    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function findInfo(int $quotationId){
        $sql = 'SELECT * FROM get_quotationinfo(:id)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $quotationId]);
        return $stmt->fetch();
    }
}