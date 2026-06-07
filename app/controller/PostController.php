<?php

    require_once __DIR__ . '/../../app/model/Post.php';
    require_once __DIR__ . '/../../app/model/Categoria.php';
    require_once __DIR__ . '/../../app/model/Comentario.php';
    require_once __DIR__ . '/../../core/Seguranca.php';

    class PostController {

        public function home() {
            $modeloPost      = new Post();
            $modeloCategoria = new Categoria();
            $categorias      = $modeloCategoria->listar();

            $idCategoria = isset($_GET['categoria']) ? (int)$_GET['categoria'] : null;

            if ($idCategoria) {
                $posts = $modeloPost->listarPorCategoria($idCategoria);
            } else {
                $posts = $modeloPost->listar();
            }

            require_once __DIR__ . '/../view/home.php';
        }

        public function criar() {
            Seguranca::exigeLogin();

            $modeloCategoria = new Categoria();
            $categorias      = $modeloCategoria->listar();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                if (!Seguranca::validarToken($_POST['token_csrf'])) {
                    $_SESSION['erro'] = 'Token inválido.';
                    header('Location: index.php?pagina=criar-post');
                    exit;
                }

                $titulo      = Seguranca::limpar($_POST['titulo']);
                $conteudo    = Seguranca::limpar($_POST['conteudo']);
                $idCategoria = (int)$_POST['id_categoria'];

                if (strlen($titulo) < 5) {
                    $_SESSION['erro'] = 'Título muito curto.';
                    header('Location: index.php?pagina=criar-post');
                    exit;
                }

                if (strlen($conteudo) < 10) {
                    $_SESSION['erro'] = 'Conteúdo muito curto.';
                    header('Location: index.php?pagina=criar-post');
                    exit;
                }

                $modelo = new Post();
                $modelo->salvar($titulo, $conteudo, $_SESSION['usuario_id'], $idCategoria);

                $_SESSION['sucesso'] = 'Post publicado!';
                header('Location: index.php?pagina=home');
                exit;
            }

            require_once __DIR__ . '/../view/criar_post.php';
        }

        public function ver() {
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

            $modeloPost      = new Post();
            $modeloComentario = new Comentario();

            $post = $modeloPost->buscarPorId($id);

            if (!$post) {
                $_SESSION['erro'] = 'Post não encontrado.';
                header('Location: index.php?pagina=home');
                exit;
            }

            $comentarios = $modeloComentario->listarPorPost($id);

            require_once __DIR__ . '/../view/detalhe_post.php';
        }

        public function deletar() {
            Seguranca::exigeLogin();

            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

            $modelo = new Post();
            $modelo->deletar($id, $_SESSION['usuario_id']);

            $_SESSION['sucesso'] = 'Post removido.';
            header('Location: index.php?pagina=home');
            exit;
        }
    }
?>