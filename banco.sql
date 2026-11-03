-- Importe uma vez em uma instalação nova. Não apaga bancos existentes.
CREATE DATABASE IF NOT EXISTS academia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE academia;

CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE planos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    valor DECIMAL(10,2) UNSIGNED NOT NULL
) ENGINE=InnoDB;

CREATE TABLE alunos (
    id_aluno INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    idade TINYINT UNSIGNED NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT TRUE,
    telefone VARCHAR(25) NOT NULL,
    imagem_url VARCHAR(500) NOT NULL DEFAULT '',
    plano_id INT UNSIGNED NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_alunos_planos FOREIGN KEY (plano_id) REFERENCES planos(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Hash gerado com password_hash('Academia@123', PASSWORD_DEFAULT).
-- Os cinco usuários didáticos usam a mesma senha: Academia@123.
INSERT INTO usuarios (nome,email,senha) VALUES
('Administrador','admin@academia.com','$2y$10$y1sZBtKtjtgLlx1PrgHCse0YSDaM0yiaF0UpWGxoyLHCMI1qP8gay'),
('Ana Recepção','ana@academia.com','$2y$10$y1sZBtKtjtgLlx1PrgHCse0YSDaM0yiaF0UpWGxoyLHCMI1qP8gay'),
('Bruno Gestão','bruno@academia.com','$2y$10$y1sZBtKtjtgLlx1PrgHCse0YSDaM0yiaF0UpWGxoyLHCMI1qP8gay'),
('Carla Recepção','carla@academia.com','$2y$10$y1sZBtKtjtgLlx1PrgHCse0YSDaM0yiaF0UpWGxoyLHCMI1qP8gay'),
('Diego Gestão','diego@academia.com','$2y$10$y1sZBtKtjtgLlx1PrgHCse0YSDaM0yiaF0UpWGxoyLHCMI1qP8gay');

INSERT INTO planos (nome,valor) VALUES
('Mensal',99.90),('Trimestral',269.90),('Semestral',499.90),('Anual',899.90),('Musculação e aulas',149.90);

INSERT INTO alunos (nome,idade,ativo,telefone,imagem_url,plano_id) VALUES
('Alice Santos',23,TRUE,'(11) 99911-1111','',1),
('Bruno Lima',30,TRUE,'(11) 99922-2222','',2),
('Camila Souza',28,TRUE,'(11) 99933-3333','',3),
('Daniel Costa',41,FALSE,'(11) 99944-4444','',4),
('Elisa Martins',35,TRUE,'(11) 99955-5555','',5);
