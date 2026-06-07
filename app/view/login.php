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