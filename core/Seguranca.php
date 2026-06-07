<?php 

class Seguranca {

    public static function gerarToken() {
        if (empty($_SESSION['token_csrf'])) {
            $_SESSION['token_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['token_csrf'];
    }

    public static function validarToken($token) {
        return isset($_SESSION ['token_csrf']) && $_SESSION ['token_csrf'] === $token;        
    }

    public static function limpar($valor) {
        return htmlspecialchars(strip_tags(trim($valor)), ENT_QUOTES, 'UTF-8');
    }

    public static function exigeLogin() {
        if (empty($_SESSION['usuario_id'])) {
            header('Location: index.php?pagina=login');
            exit();
        }
    }
}