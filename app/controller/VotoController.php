<?php

require_once __DIR__ . '/../../app/model/Voto.php';
require_once __DIR__ . '/../../core/Seguranca.php';

class VotoController {

    public function votar() {
        Seguranca::exigeLogin();

        $idPost = isset($_GET['id'])   ? (int)$_GET['id']   : 0;
        $tipo   = isset($_GET['tipo']) ? (int)$_GET['tipo'] : 0;

        if (!in_array($tipo, array(1, -1)) || $idPost <= 0) {
            header('Location: index.php?pagina=home');
            exit;
        }

        $modelo = new Voto();
        $modelo->votar($idPost, $_SESSION['usuario_id'], $tipo);

        $volta = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php?pagina=home';
        header('Location: ' . $volta);
        exit;
    }
}
?>