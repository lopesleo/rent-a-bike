-- Banco de dados

DROP DATABASE IF EXISTS rent_a_bike;

-- Criar banco de dados rent_a_bike
CREATE DATABASE IF NOT EXISTS rent_a_bike
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE rent_a_bike;

-- Clientes
CREATE TABLE IF NOT EXISTS cliente (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  codigo         VARCHAR(50) NOT NULL UNIQUE,
  nome           VARCHAR(100) NOT NULL,
  foto           VARCHAR(255),
  data_nascimento DATE        NOT NULL,
  cpf            VARCHAR(11)  NOT NULL UNIQUE,
  telefone       VARCHAR(20),
  email          VARCHAR(100),
  endereco       VARCHAR(255),
  criado_em     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  editado_em     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
                             ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Itens (bicicletas e equipamentos)
CREATE TABLE IF NOT EXISTS item (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  codigo         VARCHAR(50) NOT NULL UNIQUE,
  modelo         VARCHAR(100) NOT NULL,
  fabricante     VARCHAR(100),
  descricao      TEXT,
  valor_hora     DECIMAL(10,2) NOT NULL CHECK (valor_hora >= 0),
  numero_seguro  VARCHAR(50),
  disponivel     BOOLEAN       NOT NULL DEFAULT TRUE,
  tipo           ENUM('BICICLETA','EQUIPAMENTO') NOT NULL,
  CHECK (
    (tipo = 'BICICLETA'  AND numero_seguro IS NOT NULL)
    OR
    (tipo = 'EQUIPAMENTO' AND numero_seguro IS NULL)
  ),
  criado_em     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  editado_em     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                             ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usuários (para autenticação)
CREATE TABLE usuario (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  login        VARCHAR(255) NOT NULL UNIQUE,
  senha_hash   CHAR(128)    NOT NULL,
  salt         CHAR(32)     NOT NULL,
  criado_em    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  editado_em   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
                             ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS funcionario (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id  INT NOT NULL UNIQUE,
  codigo      VARCHAR(50) NOT NULL UNIQUE,
  cpf         VARCHAR(11) NOT NULL UNIQUE,
  nome        VARCHAR(100) NOT NULL,
  telefone    VARCHAR(20),
  email       VARCHAR(100),
  cargo       ENUM('GERENTE','ATENDENTE','MECANICO') NOT NULL,
  criado_em   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  editado_em  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
               ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_funcionario_usuario
    FOREIGN KEY (usuario_id)
    REFERENCES usuario(id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Locações
CREATE TABLE IF NOT EXISTS locacao (
  id                       INT AUTO_INCREMENT PRIMARY KEY,
  cliente_id               INT NOT NULL,
  funcionario_id           INT NOT NULL,
  data_hora_locacao        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  horas_contratadas        INT      NOT NULL CHECK (horas_contratadas > 0),
  data_hora_entrega_prevista DATETIME NOT NULL,
  desconto_aplicado        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  valor_total_previsto              DECIMAL(10,2) NOT NULL CHECK (valor_total_previsto >= 0),
  status                   ENUM('EM_ANDAMENTO','FINALIZADA','CANCELADA')
                             NOT NULL DEFAULT 'EM_ANDAMENTO',
  INDEX idx_locacao_cliente     (cliente_id),
  INDEX idx_locacao_funcionario (funcionario_id),
  FOREIGN KEY (cliente_id)
    REFERENCES cliente(id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  FOREIGN KEY (funcionario_id)
    REFERENCES funcionario(id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Itens por locação (N:N)
CREATE TABLE IF NOT EXISTS locacao_item (
  locacao_id  INT            NOT NULL,
  item_id     INT            NOT NULL,
  valor_hora  DECIMAL(10,2)  NOT NULL CHECK (valor_hora >= 0),
  limpeza_aplicada    BOOLEAN      NOT NULL DEFAULT FALSE,
  valor_taxa_limpeza DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  PRIMARY KEY (locacao_id, item_id),
  INDEX idx_locacao_item_item (item_id),
  FOREIGN KEY (locacao_id)
    REFERENCES locacao(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (item_id)
    REFERENCES item(id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Devoluções
CREATE TABLE IF NOT EXISTS devolucao (
  id                 INT            AUTO_INCREMENT PRIMARY KEY,
  locacao_id         INT            NOT NULL,
  funcionario_id     INT            NOT NULL,
  data_hora          DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  horas_usadas       INT            NOT NULL CHECK (horas_usadas >= 0),
  desconto_aplicado  DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  valor_pago         DECIMAL(10,2)  NOT NULL CHECK (valor_pago >= 0),
  INDEX idx_devolucao_locacao (locacao_id),
  FOREIGN KEY (locacao_id)
    REFERENCES locacao(id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  FOREIGN KEY (funcionario_id)
    REFERENCES funcionario(id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Avarias
CREATE TABLE avaria (
  id INT AUTO_INCREMENT PRIMARY KEY,
  devolucao_id INT NOT NULL,
  item_id INT NOT NULL,
  funcionario_id INT NOT NULL,
  data_hora DATETIME NOT NULL,
  descricao TEXT NOT NULL,
  valor DECIMAL(10,2) NOT NULL,
  foto VARCHAR(255) NOT NULL,
  INDEX idx_avaria_devolucao (devolucao_id),
  INDEX idx_avaria_item (item_id),
  INDEX idx_avaria_funcionario (funcionario_id),
  CONSTRAINT fk_avaria_devolucao
    FOREIGN KEY (devolucao_id) REFERENCES devolucao(id),
  CONSTRAINT fk_avaria_item
    FOREIGN KEY (item_id) REFERENCES item(id),
  CONSTRAINT fk_avaria_funcionario
    FOREIGN KEY (funcionario_id) REFERENCES funcionario(id)
) ENGINE=InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
