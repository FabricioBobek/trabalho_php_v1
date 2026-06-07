<?php require_once __DIR__ . '/../layout/cabecalho.php'; ?>

<div class="caixa-form">
    <h2>Editar Comentário</h2>
    <form method="POST" action="index.php?pagina=atualizar-comentario">
        <input type="hidden" name="token_csrf" value="<?= $token ?>">
        <input type="hidden" name="id"      value="<?= $dados['comentario']['id'] ?>">
        <input type="hidden" name="id_post" value="<?= $dados['comentario']['id_post'] ?>">
        <label>Comentário</label>
        <textarea name="texto" rows="4" required><?= htmlspecialchars($dados['comentario']['texto']) ?></textarea>
        <button type="submit">Salvar</button>
    </form>
</div>

<?php require_once __DIR__ . '/../layout/rodape.php'; ?>