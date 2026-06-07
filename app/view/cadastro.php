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