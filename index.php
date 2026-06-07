<?php

session_start();

require_once __DIR__ . '/core/Conexao.php';
require_once __DIR__ . '/core/Seguranca.php';

require_once __DIR__ . '/app/model/Usuario.php';
require_once __DIR__ . '/app/model/Post.php';
require_once __DIR__ . '/app/model/Categoria.php';
require_once __DIR__ . '/app/model/Comentario.php';
require_once __DIR__ . '/app/model/Voto.php';

require_once __DIR__ . '/app/controller/UsuarioController.php';
require_once __DIR__ . '/app/controller/PostController.php';
require_once __DIR__ . '/app/controller/ComentarioController.php';
require_once __DIR__ . '/app/controller/VotoController.php';

$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 'home';

$usuCtrl  = new UsuarioController();
$postCtrl = new PostController();
$comCtrl  = new ComentarioController();
$votCtrl  = new VotoController();

switch ($pagina) {
    case 'home':
        $postCtrl->home();
        break;
    case 'login':
        $usuCtrl->login();
        break;
    case 'cadastro':
        $usuCtrl->cadastro();
        break;
    case 'logout':
        $usuCtrl->logout();
        break;
    case 'criar-post':
        $postCtrl->criar();
        break;
    case 'ver-post':
        $postCtrl->ver();
        break;
    case 'deletar-post':
        $postCtrl->deletar();
        break;
    case 'votar':
        $votCtrl->votar();
        break;
    case 'comentar':
        $comCtrl->comentar();
        break;
    case 'deletar-comentario':
        $comCtrl->deletar();
        break;
    case 'sobre':
        require_once __DIR__ . '/app/view/sobre.php';
        break;
    case 'editar-comentario':
        $comCtrl->editar();
        break;
    case 'atualizar-comentario':
        $comCtrl->atualizar();
        break;
    default:
        echo '<h2>Página não encontrada.</h2>';
}