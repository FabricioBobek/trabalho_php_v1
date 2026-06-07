<?php

require_once __DIR__ . '/../../app/model/Usuario.php';
require_once __DIR__ . '/../../core/Seguranca.php';

class UsuarioController {

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if (!Seguranca::validarToken($_POST['token_csrf'])) {
                $_SESSION['erro'] = 'Token inválido, tente novamente.';
                header('Location: index.php?pagina=login');
                exit;
            }

            $email = Seguranca::limpar($_POST['email']);
            $senha = $_POST['senha'];

            $modelo  = new Usuario();
            $usuario = $modelo->buscarPorEmail($email);

            if ($usuario && password_verify($senha, $usuario['senha'])) {
                $_SESSION['usuario_id']   = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_tipo'] = $usuario['tipo'];
                header('Location: index.php?pagina=home');
                exit;
            }

            $_SESSION['erro'] = 'E-mail ou senha incorretos.';
            header('Location: index.php?pagina=login');
            exit;
        }

        require_once __DIR__ . '/../view/login.php';
    }

    public function cadastro() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if (!Seguranca::validarToken($_POST['token_csrf'])) {
                $_SESSION['erro'] = 'Token inválido, tente novamente.';
                header('Location: index.php?pagina=cadastro');
                exit;
            }

            $nome  = Seguranca::limpar($_POST['nome']);
            $email = Seguranca::limpar($_POST['email']);
            $senha = $_POST['senha'];

            if (strlen($nome) < 3) {
                $_SESSION['erro'] = 'Nome precisa ter pelo menos 3 caracteres.';
                header('Location: index.php?pagina=cadastro');
                exit;
            }

            if (strlen($senha) < 6) {
                $_SESSION['erro'] = 'Senha precisa ter pelo menos 6 caracteres.';
                header('Location: index.php?pagina=cadastro');
                exit;
            }

            $modelo = new Usuario();

            if ($modelo->emailExiste($email)) {
                $_SESSION['erro'] = 'Esse e-mail já está cadastrado.';
                header('Location: index.php?pagina=cadastro');
                exit;
            }

            $modelo->salvar($nome, $email, $senha);
            $_SESSION['sucesso'] = 'Conta criada! Faça login.';
            header('Location: index.php?pagina=login');
            exit;
        }

        require_once __DIR__ . '/../view/cadastro.php';
    }

    public function logout() {
        session_destroy();
        header('Location: index.php?pagina=login');
        exit;
    }
}