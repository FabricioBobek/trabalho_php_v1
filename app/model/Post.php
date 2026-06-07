<?php

    require_once __DIR__ . '/../../core/Conexao.php';

    class Post {

        private $db;

        public function __construct() {
            $this->db = Conexao::obter();
        }

        public function listar() {
            $sql = 'SELECT p.*, u.nome AS autor, c.nome AS categoria,
                        COALESCE(SUM(v.tipo), 0) AS votos
                    FROM posts p
                    JOIN usuarios u ON u.id = p.id_usuario
                    LEFT JOIN categorias c ON c.id = p.id_categoria
                    LEFT JOIN votos v ON v.id_post = p.id
                    GROUP BY p.id
                    ORDER BY p.data_criacao DESC';
            return $this->db->query($sql)->fetchAll();
        }

        public function listarPorCategoria($idCategoria) {
            $sql = 'SELECT p.*, u.nome AS autor, c.nome AS categoria,
                        COALESCE(SUM(v.tipo), 0) AS votos
                    FROM posts p
                    JOIN usuarios u ON u.id = p.id_usuario
                    LEFT JOIN categorias c ON c.id = p.id_categoria
                    LEFT JOIN votos v ON v.id_post = p.id
                    WHERE p.id_categoria = ?
                    GROUP BY p.id
                    ORDER BY p.data_criacao DESC';
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array($idCategoria));
            return $stmt->fetchAll();
        }

        public function buscarPorId($id) {
            $sql = 'SELECT p.*, u.nome AS autor, c.nome AS categoria,
                        COALESCE(SUM(v.tipo), 0) AS votos
                    FROM posts p
                    JOIN usuarios u ON u.id = p.id_usuario
                    LEFT JOIN categorias c ON c.id = p.id_categoria
                    LEFT JOIN votos v ON v.id_post = p.id
                    WHERE p.id = ?
                    GROUP BY p.id';
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array($id));
            return $stmt->fetch();
        }

        public function salvar($titulo, $conteudo, $idUsuario, $idCategoria) {
            $stmt = $this->db->prepare(
                'INSERT INTO posts (titulo, conteudo, id_usuario, id_categoria) VALUES (?, ?, ?, ?)'
            );
            return $stmt->execute(array($titulo, $conteudo, $idUsuario, $idCategoria));
        }

        public function deletar($id, $idUsuario) {
            $stmt = $this->db->prepare('DELETE FROM posts WHERE id = ? AND id_usuario = ?');
            return $stmt->execute(array($id, $idUsuario));
        }
    }
?>