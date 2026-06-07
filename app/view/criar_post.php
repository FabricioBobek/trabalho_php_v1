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