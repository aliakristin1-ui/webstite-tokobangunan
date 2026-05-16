<?php
class Koneksi {
    private $db_host = 'localhost',
            $db_user = 'root',
            $db_pass = 'kristin',
            $db_name = 'db_toko_bangunan';
    protected $db;
    function __construct() {
        try {
            $this->db = new PDO(
                "mysql:host=$this->db_host;dbname=$this->db_name;charset=utf8",
                $this->db_user,
                $this->db_pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            die('<div class="alert alert-danger m-3">Koneksi database gagal: ' . $e->getMessage() . '</div>');
        }
    }
}
?>