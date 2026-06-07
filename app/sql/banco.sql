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