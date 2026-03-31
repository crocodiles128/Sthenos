CREATE DATABASE IF NOT EXISTS Sthenos;
USE Sthenos;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL,
  nome_completo VARCHAR(255),
  peso DECIMAL(7,2),
  altura DECIMAL(7,2),
  cargo VARCHAR(100),
  status VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS auth (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL,
  senha VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS exercicios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  tutorial TEXT,
  video VARCHAR(500)
);

CREATE TABLE IF NOT EXISTS treinos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  personal_id INT NOT NULL,
  status VARCHAR(20) NOT NULL,
  data_treino DATETIME DEFAULT CURRENT_TIMESTAMP,
  data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (personal_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    treino_id INT NOT NULL,
    data_hora DATETIME NOT NULL,
    status ENUM('pendente', 'confirmado', 'cancelado', 'realizado') DEFAULT 'pendente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES users(id),
    FOREIGN KEY (treino_id) REFERENCES treinos(id)
);

CREATE TABLE IF NOT EXISTS treinos_realizados (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  data_treino DATE NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS treino_exercicios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  treino_id INT NOT NULL,
  exercicio_id INT NOT NULL,
  series INT,
  repeticoes INT,
  carga DECIMAL(7,2),
  FOREIGN KEY (treino_id) REFERENCES treinos(id),
  FOREIGN KEY (exercicio_id) REFERENCES exercicios(id)
);

CREATE TABLE IF NOT EXISTS checkins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  data DATE,
  exercicios_id INT,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (exercicios_id) REFERENCES exercicios(id)
);

CREATE TABLE IF NOT EXISTS evolucao (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  valor_anterior DECIMAL(12,2),
  valor_novo DECIMAL(12,2),
  data DATE,
  FOREIGN KEY (user_id) REFERENCES users(id)
);