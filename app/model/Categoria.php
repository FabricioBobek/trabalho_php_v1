<?php

    require_once __DIR__ . '/../../core/Conexao.php';

    class Categoria {

        private $db;

        public function __construct() {
            $this->db = Conexao::obter();
        }

        public function listar() {
            return $this->db->query('SELECT * FROM categorias ORDER BY nome')->fetchAll();
        }

        public function buscarPorId($id) {
            $stmt = $this->db->prepare('SELECT * FROM categorias WHERE id = ?');
            $stmt->execute(array($id));
            return $stmt->fetch();
        }
    }
?>