<?php

require_once __DIR__ . '/../../core/Conexao.php';

class Comentario {

    private $db;

    public function __construct() {
        $this->db = Conexao::obter();
    }

    public function listarPorPost($idPost) {
        $sql = 'SELECT c.*, u.nome AS autor
                FROM comentarios c
                JOIN usuarios u ON u.id = c.id_usuario
                WHERE c.id_post = ?
                ORDER BY c.data_criacao ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array($idPost));
        return $stmt->fetchAll();
    }

    public function buscarPorId($id) {
        $sql = 'SELECT c.*, u.nome AS autor
                FROM comentarios c
                JOIN usuarios u ON u.id = c.id_usuario
                WHERE c.id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array($id));
        return $stmt->fetch();
    }

    public function salvar($texto, $idPost, $idUsuario) {
        $stmt = $this->db->prepare(
            'INSERT INTO comentarios (texto, id_post, id_usuario) VALUES (?, ?, ?)'
        );
        return $stmt->execute(array($texto, $idPost, $idUsuario));
    }

    public function atualizar($id, $texto, $idUsuario) {
        $stmt = $this->db->prepare(
            'UPDATE comentarios SET texto = ? WHERE id = ? AND id_usuario = ?'
        );
        return $stmt->execute(array($texto, $id, $idUsuario));
    }

    public function deletar($id, $idUsuario) {
        $stmt = $this->db->prepare(
            'DELETE FROM comentarios WHERE id = ? AND id_usuario = ?'
        );
        return $stmt->execute(array($id, $idUsuario));
    }

    public function contarPorPost($idPost) {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM comentarios WHERE id_post = ?'
        );
        $stmt->execute(array($idPost));
        return (int) $stmt->fetchColumn();
    }

    public function pertenceAoUsuario($id, $idUsuario) {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM comentarios WHERE id = ? AND id_usuario = ?'
        );
        $stmt->execute(array($id, $idUsuario));
        return (int) $stmt->fetchColumn() > 0;
    }
     
    public function listarPorUsuario($idUsuario) {
        $sql = 'SELECT c.*, p.titulo AS titulo_post
                FROM comentarios c
                JOIN posts p ON p.id = c.id_post
                WHERE c.id_usuario = ?
                ORDER BY c.data_criacao DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array($idUsuario));
        return $stmt->fetchAll();
    }
}
?>