<?php

require_once __DIR__ . '/../../app/model/Comentario.php';
require_once __DIR__ . '/../../core/Seguranca.php';

class ComentarioController {

    public function comentar() {
        Seguranca::exigeLogin();

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?pagina=home');
            exit;
        }

        if (!Seguranca::validarToken($_POST['token_csrf'])) {
            $_SESSION['erro'] = 'Token inválido.';
            header('Location: index.php?pagina=home');
            exit;
        }

        $texto  = Seguranca::limpar($_POST['texto']);
        $idPost = (int)$_POST['id_post'];

        if (strlen($texto) < 3) {
            $_SESSION['erro'] = 'Comentário muito curto.';
            header('Location: index.php?pagina=ver-post&id=' . $idPost);
            exit;
        }

        $modelo = new Comentario();
        $modelo->salvar($texto, $idPost, $_SESSION['usuario_id']);

        header('Location: index.php?pagina=ver-post&id=' . $idPost);
        exit;
    }
    
    public function deletar() {
        Seguranca::exigeLogin();

        $id     = (int)$_GET['id'];
        $idPost = (int)$_GET['id_post'];

        $modelo = new Comentario();
        $modelo->deletar($id, $_SESSION['usuario_id']);

        header('Location: index.php?pagina=ver-post&id=' . $idPost);
        exit;
    }

    public function editar() {
        Seguranca::exigeLogin();

        $id = (int)$_GET['id'];

        $modelo     = new Comentario();
        $comentario = $modelo->buscarPorId($id);

        if (!$comentario || $comentario['id_usuario'] != $_SESSION['usuario_id']) {
            $_SESSION['erro'] = 'Comentário não encontrado.';
            header('Location: index.php?pagina=home');
            exit;
        }

        $dados['comentario'] = $comentario;
        require __DIR__ . '/../../app/view/editar_comentario.php';
    }

    public function atualizar() {
        Seguranca::exigeLogin();

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            header('Location: index.php?pagina=home');
            exit;
        }

        if (!Seguranca::validarToken($_POST['token_csrf'])) {
            $_SESSION['erro'] = 'Token inválido.';
            header('Location: index.php?pagina=home');
            exit;
        }

        $id     = (int)$_POST['id'];
        $idPost = (int)$_POST['id_post'];
        $texto  = Seguranca::limpar($_POST['texto']);

        if (strlen($texto) < 3) {
            $_SESSION['erro'] = 'Comentário muito curto.';
            header('Location: index.php?pagina=ver-post&id=' . $idPost);
            exit;
        }

        $modelo = new Comentario();

        if (!$modelo->pertenceAoUsuario($id, $_SESSION['usuario_id'])) {
            $_SESSION['erro'] = 'Ação não permitida.';
            header('Location: index.php?pagina=ver-post&id=' . $idPost);
            exit;
        }

        $modelo->atualizar($id, $texto, $_SESSION['usuario_id']);

        header('Location: index.php?pagina=ver-post&id=' . $idPost);
        exit;
    }
}
?>