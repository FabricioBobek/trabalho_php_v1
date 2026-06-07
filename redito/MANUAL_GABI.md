# Manual — Gabi

Você cuida de comentários, votos e a página de detalhe do post (onde aparecem os dois).

---

## Seus arquivos

- `app/model/Comentario.php`
- `app/model/Voto.php`
- `app/controller/ComentarioController.php`
- `app/controller/VotoController.php`
- `app/view/detalhe_post.php`

Espere o Jean criar `Conexao.php` e `Seguranca.php` antes de começar.
A view `detalhe_post.php` também depende do `Post.php` do Fabrício.

---

## app/model/Comentario.php

```php
<?php

require_once __DIR__ . '/../../core/Conexao.php';

class Comentario {

    private $db;

    public function __construct() {
        $this->db = Conexao::obter();
    }

    public function listarPorPost($idPost) {
        $sql = 'SELECT c.*, u.nome AS autor
                FROM comentarios c
                JOIN usuarios u ON u.id = c.id_usuario
                WHERE c.id_post = ?
                ORDER BY c.data_criacao ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array($idPost));
        return $stmt->fetchAll();
    }

    public function salvar($texto, $idPost, $idUsuario) {
        $stmt = $this->db->prepare(
            'INSERT INTO comentarios (texto, id_post, id_usuario) VALUES (?, ?, ?)'
        );
        return $stmt->execute(array($texto, $idPost, $idUsuario));
    }

    public function deletar($id, $idUsuario) {
        $stmt = $this->db->prepare(
            'DELETE FROM comentarios WHERE id = ? AND id_usuario = ?'
        );
        return $stmt->execute(array($id, $idUsuario));
    }
}
```

---

## app/model/Voto.php

```php
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
}
```

---

## app/controller/ComentarioController.php

```php
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
}
```

---

## app/controller/VotoController.php

```php
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
```

---

## app/view/detalhe_post.php

```php
<?php require_once __DIR__ . '/layout/cabecalho.php'; ?>

<!-- Post completo -->
<div class="post-completo">
    <div style="display:flex;gap:16px;align-items:flex-start;">
        <div class="post-votos" style="background:#f6f7f8;padding:8px;border-radius:4px;text-align:center;">
            <a href="index.php?pagina=votar&id=<?= $post['id'] ?>&tipo=1">&#9650;</a><br>
            <span class="num-votos"><?= $post['votos'] ?></span><br>
            <a href="index.php?pagina=votar&id=<?= $post['id'] ?>&tipo=-1">&#9660;</a>
        </div>
        <div style="flex:1;">
            <h2><?= htmlspecialchars($post['titulo']) ?></h2>
            <div class="meta">
                r/<?= htmlspecialchars($post['categoria'] ?? 'geral') ?>
                &bull; por <?= htmlspecialchars($post['autor']) ?>
                &bull; <?= date('d/m/Y H:i', strtotime($post['data_criacao'])) ?>
            </div>
            <div class="conteudo">
                <?= nl2br(htmlspecialchars($post['conteudo'])) ?>
            </div>
            <?php if (!empty($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $post['id_usuario']): ?>
                <div style="margin-top:12px;">
                    <a href="index.php?pagina=deletar-post&id=<?= $post['id'] ?>"
                       onclick="return confirm('Remover este post?')"
                       style="color:#c0392b;font-size:12px;text-decoration:none;">
                        Remover post
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Comentários -->
<h3 style="margin-bottom:12px;font-size:15px;">
    <?= count($comentarios) ?> comentário(s)
</h3>

<?php foreach ($comentarios as $com): ?>
    <div class="comentario">
        <span class="com-autor"><?= htmlspecialchars($com['autor']) ?></span>
        <span class="com-data"><?= date('d/m/Y H:i', strtotime($com['data_criacao'])) ?></span>
        <?php if (!empty($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $com['id_usuario']): ?>
            <a href="index.php?pagina=deletar-comentario&id=<?= $com['id'] ?>&id_post=<?= $post['id'] ?>"
               onclick="return confirm('Remover comentário?')"
               class="com-remover">remover</a>
        <?php endif; ?>
        <p><?= nl2br(htmlspecialchars($com['texto'])) ?></p>
    </div>
<?php endforeach; ?>

<!-- Formulário de comentário -->
<?php if (!empty($_SESSION['usuario_id'])): ?>
    <div class="form-comentario">
        <h4>Deixar comentário</h4>
        <form method="POST" action="index.php?pagina=comentar">
            <input type="hidden" name="token_csrf" value="<?= $token ?>">
            <input type="hidden" name="id_post"    value="<?= $post['id'] ?>">
            <textarea name="texto" rows="3" placeholder="Escreva aqui..." required></textarea>
            <button type="submit">Comentar</button>
        </form>
    </div>
<?php else: ?>
    <p style="margin-top:14px;font-size:13px;">
        <a href="index.php?pagina=login" style="color:#ff4500;">Faça login</a> para comentar.
    </p>
<?php endif; ?>

<?php require_once __DIR__ . '/layout/rodape.php'; ?>
```

---

## Para a defesa — o que você deve saber explicar

- **Por que `listarPorPost` filtra por `id_post`?** Para não carregar todos os comentários do banco, só os do post que está sendo visto.
- **O que o `ORDER BY data_criacao ASC` faz?** Mostra os comentários do mais antigo pro mais novo (ordem cronológica).
- **Como funciona o toggle de voto?** Se o usuário não votou ainda, insere. Se votou com o mesmo tipo, remove. Se votou com tipo diferente, atualiza.
- **Por que `in_array($tipo, array(1, -1))`?** Valida que o voto só pode ser +1 ou -1 — evita que alguém mande um número qualquer pela URL.
- **O que `nl2br` faz?** Transforma as quebras de linha do texto em `<br>` para aparecer certo no HTML.
- **Por que `htmlspecialchars` em tudo que exibe?** Para evitar XSS — se alguém digitar `<script>alert(1)</script>` num comentário, aparece como texto e não executa.
