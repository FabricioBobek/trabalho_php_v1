<?php

require_once __DIR__ . '/../../core/Conexao.php';

class Voto {

    private $db;

    public function __construct() {
        $this->db = Conexao::obter();
    }

    public function buscarVoto($idPost, $idUsuario) {
        $stmt = $this->db->prepare(
            'SELECT * FROM votos WHERE id_post = ? AND id_usuario = ?'
        );
        $stmt->execute(array($idPost, $idUsuario));
        return $stmt->fetch();
    }

    public function votar($idPost, $idUsuario, $tipo) {
        $votoAtual = $this->buscarVoto($idPost, $idUsuario);

        if ($votoAtual === false) {
            $stmt = $this->db->prepare(
                'INSERT INTO votos (id_post, id_usuario, tipo) VALUES (?, ?, ?)'
            );
            $stmt->execute(array($idPost, $idUsuario, $tipo));

        } elseif ($votoAtual['tipo'] == $tipo) {
            $stmt = $this->db->prepare(
                'DELETE FROM votos WHERE id_post = ? AND id_usuario = ?'
            );
            $stmt->execute(array($idPost, $idUsuario));

        } else {
            $stmt = $this->db->prepare(
                'UPDATE votos SET tipo = ? WHERE id_post = ? AND id_usuario = ?'
            );
            $stmt->execute(array($tipo, $idPost, $idUsuario));
        }
    }

    public function contarPorPost($idPost) {
        $stmt = $this->db->prepare(
            'SELECT tipo, COUNT(*) AS total FROM votos WHERE id_post = ? GROUP BY tipo'
        );
        $stmt->execute(array($idPost));

        $resultado = array(1 => 0, -1 => 0);
        foreach ($stmt->fetchAll() as $linha) {
        $resultado[(int)$linha['tipo']] = (int) $linha['total'];
        }

        return $resultado;
    }

    public function pontuacao($idPost) {
        $contagem = $this->contarPorPost($idPost);
        return $contagem['upvote'] - $contagem['downvote'];
    }

    public function listarPorUsuario($idUsuario) {
        $sql = 'SELECT v.*, p.titulo AS titulo_post
                FROM votos v
                JOIN posts p ON p.id = v.id_post
                WHERE v.id_usuario = ?
                ORDER BY v.id DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array($idUsuario));
        return $stmt->fetchAll();
    }
}
?>