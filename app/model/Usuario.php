<?php

require_once __DIR__ . '/../../core/Conexao.php';

class Usuario {

    private $db;

    public function __construct() {
        $this->db = Conexao::obter();
    }

    public function buscarPorEmail($email) {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE email = ?');
        $stmt->execute(array($email));
        return $stmt->fetch();
    }

    public function buscarPorId($id) {
        $stmt = $this->db->prepare('SELECT id, nome, email, tipo FROM usuarios WHERE id = ?');
        $stmt->execute(array($id));
        return $stmt->fetch();
    }

    public function salvar($nome, $email, $senha) {
        $hash = password_hash($senha, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare('INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)');
        return $stmt->execute(array($nome, $email, $hash));
    }

    public function emailExiste($email) {
        $stmt = $this->db->prepare('SELECT id FROM usuarios WHERE email = ?');
        $stmt->execute(array($email));
        return $stmt->fetch() !== false;
    }
}