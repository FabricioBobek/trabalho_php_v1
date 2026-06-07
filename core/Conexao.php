<?php 

class Conexao {
    private static $instancia = null;

    private function __construct() {}

    public static function obter(){
        if (self::$instancia == null) {
            try {
                self::$instancia = new PDO (
                    'mysql:host=localhost;dbname=redito;charset=utf8mb4', 'root', '',
                    array(
                    PDO::ATTR_ERRMODE             => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE  => PDO::FETCH_ASSOC
                    )
                );
            } catch (PDOException $e) {
                die('Erro de conexão: ' . $e->getMessage()); 
            }    
        }
        return self::$instancia;
    }
}