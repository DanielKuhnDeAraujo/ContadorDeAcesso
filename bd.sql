CREATE DATABASE IF NOT EXISTS BD DEFAULT CHARSET utf8mb4;
USE BD;
CREATE TABLE IF NOT EXISTS usuarios (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE,
    birthdate DATE NOT NULL,
    senha VARCHAR(255) NOT NULL   -- guarda o hash (password_hash), nunca a senha pura
) DEFAULT CHARSET=utf8mb4;
-- Se a tabela antiga já existe, rode antes:
-- DROP TABLE usuarios;   (as senhas antigas estavam em texto puro e não servem mais)
