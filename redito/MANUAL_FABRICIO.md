# Manual — Fabrício

Você cuida do banco de dados, do CSS e de tudo relacionado a posts e categorias.

---

## Seus arquivos

- `sql/banco.sql`
- `public/estilo.css`
- `app/model/Categoria.php`
- `app/model/Post.php`
- `app/controller/PostController.php`
- `app/view/home.php`
- `app/view/criar_post.php`
- `app/view/sobre.php`

Crie o banco primeiro — o Jean e a Gabi precisam que ele exista para testar.

---

## sql/banco.sql

Crie esse arquivo e importe no phpMyAdmin (Importar > selecionar arquivo).

```sql
CREATE DATABASE IF NOT EXISTS redito
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE redito;

CREATE TABLE usuarios (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    nome         VARCHAR(100) NOT NULL,
    email        VARCHAR(100) NOT NULL UNIQUE,
    senha        VARCHAR(255) NOT NULL,
    tipo         ENUM('comum', 'admin') DEFAULT 'comum',
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categorias (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL
);

CREATE TABLE posts (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    titulo       VARCHAR(200) NOT NULL,
    conteudo     TEXT NOT NULL,
    id_usuario   INT NOT NULL,
    id_categoria INT,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario)   REFERENCES usuarios(id),
    FOREIGN KEY (id_categoria) REFERENCES categorias(id)
);

CREATE TABLE comentarios (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    texto        TEXT NOT NULL,
    id_post      INT NOT NULL,
    id_usuario   INT NOT NULL,
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_post)    REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

CREATE TABLE votos (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    id_post    INT NOT NULL,
    id_usuario INT NOT NULL,
    tipo       TINYINT NOT NULL,
    UNIQUE KEY voto_unico (id_post, id_usuario),
    FOREIGN KEY (id_post)    REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

INSERT INTO categorias (nome) VALUES ('Geral'), ('Tecnologia'), ('Humor'), ('Noticias'), ('Perguntas');
```

---

## public/estilo.css

CSS simples com tema Reddit (laranja, fundo cinza claro, cards brancos).

```css
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background-color: #dae0e6;
    color: #1c1c1c;
    font-size: 14px;
}

/* Topo */
.topo {
    background-color: #ff4500;
    padding: 0 20px;
    height: 48px;
    display: flex;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 100;
}

.topo-inner {
    width: 100%;
    max-width: 1100px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    color: white;
    font-size: 18px;
    font-weight: bold;
    text-decoration: none;
    letter-spacing: 1px;
}

.topo nav a {
    color: white;
    text-decoration: none;
    margin-left: 16px;
    font-size: 13px;
}

.topo nav a:hover {
    text-decoration: underline;
}

.nav-usuario {
    color: rgba(255,255,255,0.85);
    margin-left: 16px;
    font-size: 13px;
}

/* Container principal */
.pagina {
    max-width: 1100px;
    margin: 20px auto;
    padding: 0 16px;
}

/* Layout de duas colunas (só na home) */
.layout-dois {
    display: flex;
    gap: 24px;
}

/* Avisos */
.aviso {
    padding: 10px 14px;
    border-radius: 4px;
    margin-bottom: 14px;
    font-size: 13px;
    width: 100%;
}

.aviso-erro { background: #fde8e8; color: #c0392b; border: 1px solid #e8a0a0; }
.aviso-ok   { background: #e6f4ea; color: #27ae60; border: 1px solid #a0d8a8; }

/* Layout de duas colunas */
.coluna-posts {
    flex: 1;
    min-width: 0;
}

.sidebar {
    width: 270px;
    flex-shrink: 0;
}

.sidebar-bloco {
    background: white;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 14px;
    margin-bottom: 14px;
}

.sidebar-bloco h4 {
    font-size: 13px;
    font-weight: bold;
    margin-bottom: 10px;
    color: #ff4500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.sidebar-bloco ul {
    list-style: none;
}

.sidebar-bloco ul li {
    margin-bottom: 6px;
}

.sidebar-bloco ul li a {
    color: #333;
    text-decoration: none;
    font-size: 13px;
}

.sidebar-bloco ul li a:hover {
    color: #ff4500;
}

.btn-criar {
    display: block;
    background: #ff4500;
    color: white;
    text-align: center;
    padding: 8px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 13px;
    margin-top: 10px;
}

.btn-criar:hover {
    background: #d63c00;
}

/* Cards de post */
.post-card {
    background: white;
    border: 1px solid #ccc;
    border-radius: 4px;
    margin-bottom: 10px;
    display: flex;
}

.post-card:hover {
    border-color: #999;
}

.post-votos {
    background: #f6f7f8;
    width: 40px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    padding: 8px 4px;
    gap: 4px;
    border-radius: 4px 0 0 4px;
    flex-shrink: 0;
}

.post-votos a {
    text-decoration: none;
    color: #888;
    font-size: 16px;
    line-height: 1;
}

.post-votos a:hover {
    color: #ff4500;
}

.post-votos .num-votos {
    font-size: 12px;
    font-weight: bold;
    color: #333;
}

.post-info {
    padding: 10px 12px;
    flex: 1;
}

.post-info .categoria {
    font-size: 11px;
    color: #888;
    margin-bottom: 4px;
}

.post-info .categoria a {
    color: #ff4500;
    text-decoration: none;
    font-weight: bold;
}

.post-info h3 {
    font-size: 16px;
    margin-bottom: 4px;
}

.post-info h3 a {
    color: #222;
    text-decoration: none;
}

.post-info h3 a:hover {
    color: #ff4500;
}

.post-info .meta {
    font-size: 11px;
    color: #999;
    margin-bottom: 8px;
}

.post-info .acoes a {
    font-size: 12px;
    color: #888;
    text-decoration: none;
    margin-right: 12px;
}

.post-info .acoes a:hover {
    color: #ff4500;
}

/* Formulários */
.caixa-form {
    background: white;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 24px;
    max-width: 420px;
    margin: 0 auto;
}

.caixa-form h2 {
    margin-bottom: 18px;
    font-size: 18px;
}

.caixa-form label {
    display: block;
    font-size: 12px;
    font-weight: bold;
    margin-bottom: 4px;
    color: #555;
    text-transform: uppercase;
}

.caixa-form input,
.caixa-form select,
.caixa-form textarea {
    width: 100%;
    padding: 8px 10px;
    margin-bottom: 14px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    font-family: Arial, sans-serif;
}

.caixa-form input:focus,
.caixa-form textarea:focus,
.caixa-form select:focus {
    outline: none;
    border-color: #ff4500;
}

.caixa-form button {
    width: 100%;
    padding: 10px;
    background: #ff4500;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 14px;
    cursor: pointer;
    font-weight: bold;
}

.caixa-form button:hover {
    background: #d63c00;
}

.caixa-form p {
    margin-top: 14px;
    font-size: 13px;
    text-align: center;
}

.caixa-form p a {
    color: #ff4500;
}

/* Comentários */
.comentario {
    background: white;
    border: 1px solid #e8e8e8;
    border-left: 3px solid #ff4500;
    padding: 10px 14px;
    margin-bottom: 8px;
    border-radius: 0 4px 4px 0;
}

.comentario .com-autor {
    font-size: 12px;
    font-weight: bold;
    color: #555;
}

.comentario .com-data {
    font-size: 11px;
    color: #aaa;
    margin-left: 8px;
}

.comentario p {
    margin-top: 6px;
    font-size: 14px;
    line-height: 1.5;
}

.comentario .com-remover {
    font-size: 11px;
    color: #c0392b;
    text-decoration: none;
    float: right;
}

/* Rodapé */
.rodape {
    text-align: center;
    padding: 20px;
    font-size: 12px;
    color: #aaa;
    margin-top: 30px;
}

/* Post completo */
.post-completo {
    background: white;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 16px;
    margin-bottom: 20px;
}

.post-completo h2 {
    font-size: 20px;
    margin-bottom: 8px;
}

.post-completo .meta {
    font-size: 12px;
    color: #999;
    margin-bottom: 14px;
}

.post-completo .conteudo {
    line-height: 1.7;
    font-size: 14px;
}

.form-comentario {
    background: white;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 16px;
    margin-top: 16px;
}

.form-comentario h4 {
    margin-bottom: 10px;
    font-size: 14px;
}

.form-comentario textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    font-family: Arial, sans-serif;
    margin-bottom: 8px;
    resize: vertical;
}

.form-comentario button {
    background: #ff4500;
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 13px;
}

.form-comentario button:hover {
    background: #d63c00;
}
```

---

## app/model/Categoria.php

```php
<?php

require_once __DIR__ . '/../../core/Conexao.php';

class Categoria {

    private $db;

    public function __construct() {
        $this->db = Conexao::obter();
    }

    public function listar() {
        return $this->db->query('SELECT * FROM categorias ORDER BY nome')->fetchAll();
    }

    public function buscarPorId($id) {
        $stmt = $this->db->prepare('SELECT * FROM categorias WHERE id = ?');
        $stmt->execute(array($id));
        return $stmt->fetch();
    }
}
```

---

## app/model/Post.php

```php
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
```

---

## app/controller/PostController.php

```php
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
```

---

## app/view/home.php

```php
<?php require_once __DIR__ . '/layout/cabecalho.php'; ?>

<div class="layout-dois">
<div class="coluna-posts">

    <?php if (empty($posts)): ?>
        <p>Nenhum post ainda. <a href="index.php?pagina=criar-post">Crie o primeiro!</a></p>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="post-card">
                <div class="post-votos">
                    <a href="index.php?pagina=votar&id=<?= $post['id'] ?>&tipo=1">&#9650;</a>
                    <span class="num-votos"><?= $post['votos'] ?></span>
                    <a href="index.php?pagina=votar&id=<?= $post['id'] ?>&tipo=-1">&#9660;</a>
                </div>
                <div class="post-info">
                    <div class="categoria">
                        <a href="index.php?pagina=home&categoria=<?= $post['id_categoria'] ?>">
                            r/<?= htmlspecialchars($post['categoria'] ?? 'geral') ?>
                        </a>
                    </div>
                    <h3>
                        <a href="index.php?pagina=ver-post&id=<?= $post['id'] ?>">
                            <?= htmlspecialchars($post['titulo']) ?>
                        </a>
                    </h3>
                    <div class="meta">
                        por <?= htmlspecialchars($post['autor']) ?>
                        &bull; <?= date('d/m/Y H:i', strtotime($post['data_criacao'])) ?>
                    </div>
                    <div class="acoes">
                        <a href="index.php?pagina=ver-post&id=<?= $post['id'] ?>">Comentários</a>
                        <?php if (!empty($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $post['id_usuario']): ?>
                            <a href="index.php?pagina=deletar-post&id=<?= $post['id'] ?>"
                               onclick="return confirm('Remover este post?')">Remover</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<aside class="sidebar">
    <div class="sidebar-bloco">
        <h4>Categorias</h4>
        <ul>
            <li><a href="index.php?pagina=home">Todos</a></li>
            <?php foreach ($categorias as $cat): ?>
                <li>
                    <a href="index.php?pagina=home&categoria=<?= $cat['id'] ?>">
                        r/<?= htmlspecialchars($cat['nome']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php if (!empty($_SESSION['usuario_id'])): ?>
            <a href="index.php?pagina=criar-post" class="btn-criar">+ Criar Post</a>
        <?php endif; ?>
    </div>
</aside>
</div>

<?php require_once __DIR__ . '/layout/rodape.php'; ?>
```

---

## app/view/criar_post.php

```php
<?php require_once __DIR__ . '/layout/cabecalho.php'; ?>

<div class="caixa-form" style="max-width:580px;">
    <h2>Criar Post</h2>
    <form method="POST" action="index.php?pagina=criar-post">
        <input type="hidden" name="token_csrf" value="<?= $token ?>">

        <label>Título</label>
        <input type="text" name="titulo" required>

        <label>Categoria</label>
        <select name="id_categoria">
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nome']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Conteúdo</label>
        <textarea name="conteudo" rows="6" required></textarea>

        <button type="submit">Publicar</button>
    </form>
</div>

<?php require_once __DIR__ . '/layout/rodape.php'; ?>
```

---

## app/view/sobre.php

```php
<?php require_once __DIR__ . '/layout/cabecalho.php'; ?>

<div style="background:white;border:1px solid #ccc;border-radius:4px;padding:24px;max-width:600px;">
    <h2>Sobre</h2>
    <p style="margin-top:12px;line-height:1.7;">
        Redito é um fórum desenvolvido em PHP como trabalho da faculdade.
        Usuários podem se cadastrar, criar posts, votar e comentar.
    </p>
    <h3 style="margin-top:20px;margin-bottom:8px;">Funcionalidades</h3>
    <ul style="margin-left:20px;line-height:2;">
        <li>Cadastro e login de usuários</li>
        <li>Criação e remoção de posts</li>
        <li>Upvote e downvote</li>
        <li>Comentários</li>
        <li>Filtro por categorias</li>
    </ul>
    <h3 style="margin-top:20px;margin-bottom:8px;">Tecnologias</h3>
    <ul style="margin-left:20px;line-height:2;">
        <li>PHP com PDO</li>
        <li>MySQL</li>
        <li>HTML + CSS</li>
    </ul>
</div>

<?php require_once __DIR__ . '/layout/rodape.php'; ?>
```

---

## Para a defesa — o que você deve saber explicar

- **JOIN**: une os dados de duas tabelas. O `JOIN usuarios` traz o nome do autor junto com o post, sem precisar fazer duas queries.
- **LEFT JOIN votos**: pega os votos, mas se não houver nenhum não descarta o post (diferente do JOIN normal).
- **COALESCE(SUM(...), 0)**: soma todos os votos. O COALESCE garante que retorna 0 se não tiver nenhum voto, em vez de NULL.
- **GROUP BY p.id**: necessário quando usa SUM — agrupa os resultados por post para não duplicar.
- **Por que o model não valida nada?**: o model só faz o trabalho com o banco. A validação fica no controller, que é quem recebe os dados do usuário.
