# Redito — Visão Geral do Projeto

Trabalho PHP — Desenvolvimento de Sistemas

---

## Divisão

| Pessoa | O que faz |
|---|---|
| Jean | Estrutura de pastas, conexão com banco, segurança/sessão, modelo e controller de usuário, views de login e cadastro, layout (cabeçalho/rodapé), index.php |
| Fabrício | Banco de dados, CSS, modelo e controller de post e categoria, views de home, criar post e sobre |
| Gabi | Modelo e controller de comentário e voto, view de detalhe do post |

---

## Estrutura de pastas

```
redito/
├── index.php
├── sql/
│   └── banco.sql                      (Fabrício)
├── core/
│   ├── Conexao.php                    (Jean)
│   └── Seguranca.php                  (Jean)
├── app/
│   ├── model/
│   │   ├── Usuario.php                (Jean)
│   │   ├── Post.php                   (Fabrício)
│   │   ├── Categoria.php              (Fabrício)
│   │   ├── Comentario.php             (Gabi)
│   │   └── Voto.php                   (Gabi)
│   ├── controller/
│   │   ├── UsuarioController.php      (Jean)
│   │   ├── PostController.php         (Fabrício)
│   │   ├── ComentarioController.php   (Gabi)
│   │   └── VotoController.php         (Gabi)
│   └── view/
│       ├── layout/
│       │   ├── cabecalho.php          (Jean)
│       │   └── rodape.php             (Jean)
│       ├── login.php                  (Jean)
│       ├── cadastro.php               (Jean)
│       ├── home.php                   (Fabrício)
│       ├── criar_post.php             (Fabrício)
│       ├── sobre.php                  (Fabrício)
│       └── detalhe_post.php           (Gabi)
└── public/
    └── estilo.css                     (Fabrício)
```

---

## Ordem de desenvolvimento

```
1. Fabrício cria o banco (banco.sql) e importa no phpMyAdmin
2. Jean cria Conexao.php, Seguranca.php, Usuario.php, UsuarioController.php
3. Jean cria cabecalho.php, rodape.php, login.php, cadastro.php
4. Fabrício cria Post.php, Categoria.php, PostController.php, home.php, criar_post.php, sobre.php, estilo.css
5. Gabi cria Comentario.php, Voto.php, ComentarioController.php, VotoController.php, detalhe_post.php
6. Jean cria o index.php juntando tudo
7. Testar junto
```

---

## Como rodar

1. Instalar XAMPP
2. Colocar a pasta `redito` em `C:/xampp/htdocs/`
3. Abrir `http://localhost/phpmyadmin` e importar `sql/banco.sql`
4. Acessar `http://localhost/redito/`
5. Cadastrar um usuário pelo site

---

## Páginas públicas (sem login)

- `index.php?pagina=home` — lista de posts
- `index.php?pagina=login` — login
- `index.php?pagina=cadastro` — cadastro
- `index.php?pagina=sobre` — sobre o sistema

---

## Requisitos atendidos

| Requisito | Como está |
|---|---|
| index.php com menu | index.php roteia todas as páginas |
| Banco exportado | sql/banco.sql |
| Lógica em PHP | Controllers com validação, sessão, CSRF |
| PDO | Conexao.php |
| Sessões | Login usa $_SESSION |
| CSRF | Token em todos os formulários |
| 3+ páginas públicas | Home, Login, Cadastro, Sobre |
| CRUD | Usuário, Post, Comentário |
| MVC | Model / View / Controller separados |
