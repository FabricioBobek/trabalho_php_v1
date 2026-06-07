# Manual — Jean

Você cuida de tudo relacionado a usuário: conexão com banco, sessão, login e cadastro. Também monta o esqueleto que os outros dois vão usar (cabeçalho, rodapé, index.php).

---

## Seus arquivos

- `core/Conexao.php`
- `core/Seguranca.php`
- `app/model/Usuario.php`
- `app/controller/UsuarioController.php`
- `app/view/layout/cabecalho.php`
- `app/view/layout/rodape.php`
- `app/view/login.php`
- `app/view/cadastro.php`
- `index.php`

Espere o Fabrício criar o banco antes de testar.

---

## core/Conexao.php

Classe que abre a conexão com o banco usando PDO. Usa o padrão Singleton para não abrir várias conexões ao mesmo tempo.

> **Atenção:** o usuário está como `root` e a senha como `''` (vazia). Isso funciona no XAMPP padrão. Se a sua instalação tiver senha no MySQL, troque o `''` pela sua senha.

```php
<?php

class Conexao {
    private static $instancia = null;

    private function __construct() {}

    public static function obter() {
        if (self::$instancia === null) {
            try {
                self::$instancia = new PDO(
                    'mysql:host=localhost;dbname=reddit_simples;charset=utf8mb4',
                    'root',
                    '', // <- coloque sua senha aqui se o MySQL exigir
                    array(
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    )
                );
            } catch (PDOException $e) {
                die('Erro na conexão: ' . $e->getMessage());
            }
        }
        return self::$instancia;
    }
}
```

---

## core/Seguranca.php

Centraliza proteção CSRF, sanitização de texto e verificação de login.

```php
<?php

class Seguranca {

    public static function gerarToken() {
        if (empty($_SESSION['token_csrf'])) {
            $_SESSION['token_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['token_csrf'];
    }

    public static function validarToken($token) {
        return isset($_SESSION['token_csrf']) && $_SESSION['token_csrf'] === $token;
    }

    public static function limpar($valor) {
        return htmlspecialchars(strip_tags(trim($valor)), ENT_QUOTES, 'UTF-8');
    }

    public static function exigeLogin() {
        if (empty($_SESSION['usuario_id'])) {
            header('Location: index.php?pagina=login');
            exit;
        }
    }
}
```

---

## app/model/Usuario.php

Faz as consultas de usuário no banco.

```php
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
```

---

## app/controller/UsuarioController.php

Recebe os dados dos formulários, valida e chama o model.

```php
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
```

---

## app/view/layout/cabecalho.php

Cabeçalho que todas as outras views incluem no topo.

```php
<?php $token = Seguranca::gerarToken(); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redito</title>
    <link rel="stylesheet" href="public/estilo.css">
</head>
<body>

<header class="topo">
    <div class="topo-inner">
        <a href="index.php?pagina=home" class="logo">Redito</a>
        <nav>
            <?php if (!empty($_SESSION['usuario_id'])): ?>
                <span class="nav-usuario">Olá, <?= htmlspecialchars($_SESSION['usuario_nome']) ?></span>
                <a href="index.php?pagina=criar-post">+ Criar Post</a>
                <a href="index.php?pagina=logout">Sair</a>
            <?php else: ?>
                <a href="index.php?pagina=login">Entrar</a>
                <a href="index.php?pagina=cadastro">Cadastrar</a>
            <?php endif; ?>
            <a href="index.php?pagina=sobre">Sobre</a>
        </nav>
    </div>
</header>

<main class="pagina">

<?php if (!empty($_SESSION['erro'])): ?>
    <div class="aviso aviso-erro"><?= htmlspecialchars($_SESSION['erro']) ?></div>
    <?php unset($_SESSION['erro']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['sucesso'])): ?>
    <div class="aviso aviso-ok"><?= htmlspecialchars($_SESSION['sucesso']) ?></div>
    <?php unset($_SESSION['sucesso']); ?>
<?php endif; ?>
```

---

## app/view/layout/rodape.php

```php
</main>

<footer class="rodape">
    <p>Redito &copy; <?= date('Y') ?></p>
</footer>

</body>
</html>
```

---

## app/view/login.php

```php
<?php require_once __DIR__ . '/layout/cabecalho.php'; ?>

<div class="caixa-form">
    <h2>Entrar</h2>
    <form method="POST" action="index.php?pagina=login">
        <input type="hidden" name="token_csrf" value="<?= $token ?>">

        <label>E-mail</label>
        <input type="email" name="email" required>

        <label>Senha</label>
        <input type="password" name="senha" required>

        <button type="submit">Entrar</button>
    </form>
    <p>Não tem conta? <a href="index.php?pagina=cadastro">Cadastre-se</a></p>
</div>

<?php require_once __DIR__ . '/layout/rodape.php'; ?>
```

---

## app/view/cadastro.php

```php
<?php require_once __DIR__ . '/layout/cabecalho.php'; ?>

<div class="caixa-form">
    <h2>Criar Conta</h2>
    <form method="POST" action="index.php?pagina=cadastro">
        <input type="hidden" name="token_csrf" value="<?= $token ?>">

        <label>Nome</label>
        <input type="text" name="nome" required>

        <label>E-mail</label>
        <input type="email" name="email" required>

        <label>Senha</label>
        <input type="password" name="senha" required>

        <button type="submit">Criar Conta</button>
    </form>
    <p>Já tem conta? <a href="index.php?pagina=login">Entrar</a></p>
</div>

<?php require_once __DIR__ . '/layout/rodape.php'; ?>
```

---

## index.php

Esse é o arquivo central — ele lê o parâmetro `?pagina=` na URL e chama o controller certo.
Crie esse arquivo por último, depois que todos terminarem.

```php
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
    default:
        echo '<h2>Página não encontrada.</h2>';
}
```

---

## Para a defesa — o que você deve saber explicar

- **PDO**: classe do PHP para acessar banco de forma segura. O `prepare` + `execute` evita SQL Injection.
- **Singleton**: garante que só existe uma conexão aberta com o banco.
- **CSRF**: ataque onde outro site envia formulário em nome do usuário. O token no formulário impede isso porque o site malicioso não tem o token.
- **`password_hash` / `password_verify`**: armazena a senha criptografada; nunca em texto puro.
- **`$_SESSION`**: como o sistema sabe quem está logado enquanto navega entre páginas.
- **`htmlspecialchars`**: converte `<script>` em texto, impedindo XSS.
