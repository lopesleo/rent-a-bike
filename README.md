# Rent-A-Bike - Projeto Integrador de Sistemas

**Disciplina:** Projeto Integrador de Sistemas – CEFET/RJ Nova Friburgo – 2025-1
**Professor:** Thiago Delgado Pinto
**Entrega:** 27/05/2025

---

## 1. Alunos

- **Leonardo Lopes Almeida** – [leonardo.lopes@aluno.cefet-rj.br](mailto:leonardo.lopes@aluno.cefet-rj.br)
- **Celso Dames Junior** – [celso.junior@aluno.cefet-rj.br](mailto:celso.junior@aluno.cefet-rj.br)

---

## 2. Descrição do Projeto

Aplicação web (MPA + API) para gerenciamento de locação e devolução de bicicletas e equipamentos da Rent‑A‑Bike. Funcionalidades:

1. **Nova Locação** (criação sem impressão de contrato)
2. **Listagem de Locações** (sem pesquisa nem paginação)
3. **Devolução de Locação** (sem revisão de avarias)
4. **Listagem de Devoluções** (sem pesquisa nem paginação)

Arquitetura MVP, testes unitários/integrados, análise estática de código e infraestrutura via Docker Compose.

---

## 3. Estrutura do Repositório

```
/ (raiz do projeto)
├── backend/            # API em PHP (Slim, Composer, Kahlan, PHPStan)
├── frontend/           # MPA em TypeScript (Vite, PNPM, ViTest, Playwright)
├── infra/              # Docker Compose (MariaDB + phpMyAdmin)
│   └── my.cnf          # Ajustes extras do MariaDB
├── Makefile            # Atalhos para infra, build e testes
└── README.md           # Este documento
```

---

## 4. Tecnologias Utilizadas

### Backend

- **PHP 8** + Slim Framework
- **Composer** (gerenciamento de dependências)
- **Kahlan** (BDD para testes)
- **PHPStan** (análise estática)
- **MariaDB** + phpMyAdmin

### Frontend

- **TypeScript**
- **Vite** + PNPM
- **ViTest** (unitários/integrados)
- **Playwright** (e2e)
- **Bootstrap** (CSS responsivo) – estilos e logo criados com auxílio do ChatGPT

### Infraestrutura

- **Docker** + Docker Compose v2

---

## 5. Pré-requisitos

- Docker (incluindo Docker Compose)
- Make
- Node.js (v16+) + PNPM
- PHP 8 + Composer

---

## 6. Instalação e Execução

1. **Clone o repositório**:

   ```bash
   git clone https://github.com/lopesleo/rent-a-bike.git
   cd Rent-A-Bike
   ```

2. **Suba banco e phpMyAdmin**:

   ```bash
   make infra-up
   ```

3. **Backend (não usa .env)**:

   - As credenciais de conexão estão em `src/Database/Connection.php`.
   - Se precisar ajustar, edite diretamente host, user, pass e db.

4. **Frontend**:

   - Dentro de `frontend/`, crie `/.env.local` com:

     ```env
     VITE_URL=http://localhost:8001
     ```

5. **Instalar dependências e iniciar serviços**:

   ```bash
   # Infra, API e SPA
   make up

   # Ou individualmente:
   make backend-start   # PHP embutido na porta 8001
   make frontend-start  # Vite na porta 5173
   ```

6. **Acesse**:

   - phpMyAdmin: [http://localhost:8080](http://localhost:8080)
   - API PHP: [http://localhost:8001](http://localhost:8001)
   - MPA Frontend: [http://localhost:5173](http://localhost:5173)

---

## 7. Scripts Importantes

### Makefile

| Comando                 | Ação                                                |
| ----------------------- | --------------------------------------------------- |
| `make help`             | Lista comandos disponíveis                          |
| `make infra-up`         | Inicia MariaDB + phpMyAdmin                         |
| `make infra-down`       | Encerra infra                                       |
| `make backend-install`  | `composer install`                                  |
| `make backend-start`    | `composer start` (PHP embutido)                     |
| `make backend-test`     | Executa testes com Kahlan                           |
| `make backend-analyze`  | Executa PHPStan                                     |
| `make frontend-install` | `pnpm install`                                      |
| `make frontend-start`   | `pnpm run dev` (Vite)                               |
| `make frontend-test`    | `pnpm test` / `pnpm run e2e`                        |
| `make up`               | `infra-up` + `backend-install` + `frontend-install` |
| `make down`             | `infra-down`                                        |

### Composer (backend)

| Script                   | Descrição                                 |
| ------------------------ | ----------------------------------------- |
| `composer install`       | Instala dependências                      |
| `composer dump-autoload` | Atualiza autoload                         |
| `composer start`         | Inicia servidor PHP embutido (porta 8001) |
| `composer test`          | Executa testes Kahlan                     |
| `composer check`         | Executa PHPStan                           |
| `composer db`            | Popula DB (`db:e` + `db:seed`)            |

### PNPM (frontend)

| Script              | Descrição                             |
| ------------------- | ------------------------------------- |
| `pnpm i`            | Instala as dependencias via pnpm      |
| `pnpm run dev`      | Inicia Vite em modo dev na porta 5173 |
| `pnpm run build`    | Gera build de produção                |
| `pnpm run preview`  | Serve o build localmente              |
| `pnpm run lint`     | Roda ESLint                           |
| `pnpm run lint:fix` | Roda ESLint e corrige problemas       |
| `pnpm run test`     | Executa ViTest (unitários/integrados) |
| `pnpm run e2e`      | Executa testes Playwright             |

---

## 8. Referências Bibliográficas e Recursos

- **DOCKER**. Docker Compose. Disponível em: [https://docs.docker.com/compose/](https://docs.docker.com/compose/). Acesso em: 19 abr. 2025.
- **MARIADB**. MariaDB Official. Disponível em: [https://mariadb.org/](https://mariadb.org/). Acesso em: 19 abr. 2025.
- **PHPMYADMIN**. phpMyAdmin Documentation. Disponível em: [https://www.phpmyadmin.net/docs/](https://www.phpmyadmin.net/docs/). Acesso em: 19 abr. 2025.
- **PHP**. Manual do PHP. Disponível em: [https://www.php.net/manual/pt_BR/](https://www.php.net/manual/pt_BR/). Acesso em: 19 abr. 2025.
- **COMPOSER**. Composer. Disponível em: [https://getcomposer.org/](https://getcomposer.org/). Acesso em: 19 abr. 2025.
- **KAHLAN**. Kahlan PHP Testing Framework. Disponível em: [https://kahlan.github.io/](https://kahlan.github.io/). Acesso em: 19 abr. 2025.
- **PHPSTAN**. PHPStan. Disponível em: [https://phpstan.org/](https://phpstan.org/). Acesso em: 19 abr. 2025.
- **NODE.JS**. Node.js. Disponível em: [https://nodejs.org/](https://nodejs.org/). Acesso em: 19 abr. 2025.
- **PNPM**. pnpm. Disponível em: [https://pnpm.io/](https://pnpm.io/). Acesso em: 19 abr. 2025.
- **VITE**. Vite – Next Generation Frontend Tooling. Disponível em: [https://vitejs.dev/](https://vitejs.dev/). Acesso em: 19 abr. 2025.
- **VITEST**. Vitest – Vite Native Unit Test Framework. Disponível em: [https://vitest.dev/](https://vitest.dev/). Acesso em: 19 abr. 2025.
- **PLAYWRIGHT**. Playwright. Disponível em: [https://playwright.dev/](https://playwright.dev/). Acesso em: 19 abr. 2025.
- **BOOTSTRAP**. Bootstrap docs. Disponível em: [https://getbootstrap.com/docs/](https://getbootstrap.com/docs/). Acesso em: 19 abr. 2025.
- **ARRAY MAP/FILTER/MERGE**. "Simplifying PHP arrays...". Disponível em: [https://medium.com/@jochelle.mendonca/simplifying-php-arrays-a-guide-to-array-map-array-filter-and-array-merge-ac421e358db4](https://medium.com/@jochelle.mendonca/simplifying-php-arrays-a-guide-to-array-map-array-filter-and-array-merge-ac421e358db4). Acesso em: 25 mai. 2025.
- **ARRAY_MAP**. "PHP Array Map". Disponível em: [https://honarsystems.com/php-array-map/](https://honarsystems.com/php-array-map/). Acesso em: 20 mai. 2025.
- **VITE PROXY**. Vite server proxy. Disponível em: [https://vite.dev/config/server-options#server-proxy](https://vite.dev/config/server-options#server-proxy). Acesso em: 25 mai. 2025.
- **PHP DATETIME**. "Choosing the right PHP DateTime class...". Disponível em: [https://medium.com/@jochelle.mendonca/choosing-the-right-php-datetime-class-a-practical-comparison-8f642b300a2f](https://medium.com/@jochelle.mendonca/choosing-the-right-php-datetime-class-a-practical-comparison-8f642b300a2f). Acesso em: 22 mai. 2025.

---
