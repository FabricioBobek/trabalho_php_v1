<?php require_once __DIR__ . '/layout/cabecalho.php'; ?>

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
                        
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<h3 style="margin-bottom:12px;font-size:15px;">
    <?= count($comentarios) ?> comentário(s)
</h3>

<?php foreach ($comentarios as $com): ?>
    <div class="comentario">
        <span class="com-autor"><?= htmlspecialchars($com['autor']) ?></span>
        <span class="com-data"><?= date('d/m/Y H:i', strtotime($com['data_criacao'])) ?></span>
        <?php if (!empty($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $com['id_usuario']): ?>
            <a href="index.php?pagina=editar-comentario&id=<?= $com['id'] ?>"
               class="com-editar">editar</a>
            <a href="index.php?pagina=deletar-comentario&id=<?= $com['id'] ?>&id_post=<?= $post['id'] ?>"
               onclick="return confirm('Remover comentário?')"
               class="com-remover">remover</a>
        <?php endif; ?>
        <p><?= nl2br(htmlspecialchars($com['texto'])) ?></p>
    </div>
<?php endforeach; ?>

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