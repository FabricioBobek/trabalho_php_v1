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