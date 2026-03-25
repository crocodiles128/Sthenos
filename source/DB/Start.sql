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

CREATE TABLE IF NOT EXISTS financeiro (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome_completo VARCHAR(255),
  cpf VARCHAR(20),
  valor_bruto DECIMAL(12,2),
  desconto DECIMAL(12,2),
  ultimo_pagamento DATE,
  situacao VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS exercicios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(255) NOT NULL,
  descricao TEXT,
  tutorial TEXT,
  video VARCHAR(500)
);

CREATE TABLE IF NOT EXISTS treinos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  personal INT,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (personal) REFERENCES users(id)
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