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