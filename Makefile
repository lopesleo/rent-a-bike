
COMPOSE      := docker compose
INFRA_DIR    := infra
BACKEND_DIR  := backend
FRONTEND_DIR := frontend

.PHONY: help
help:
	@echo "Disponíveis:" \
	&& echo "  make infra-up            # Inicia infra (MariaDB + phpMyAdmin)" \
	&& echo "  make infra-down          # Para infra" \
	&& echo "  make backend-install     # Instala dependências PHP (Composer)" \
	&& echo "  make backend-start       # Inicia servidor PHP (built-in)" \
	&& echo "  make backend-test        # Executa testes backend (Kahlan)" \
	&& echo "  make dump-composer       # Gera dump do compose" \
	&& echo "  make backend-analyze     # Executa análise estática (PHPStan)" \
	&& echo "  make frontend-install    # Instala dependências frontend (PNPM)" \
	&& echo "  make frontend-start      # Inicia servidor de dev (Vite)" \
	&& echo "  make frontend-test       # Executa testes frontend (ViTest/Playwright)" \
	&& echo "  make up                  # Infra + install backend + install frontend" \
	&& echo "  make down                # Para infra" \
	&& echo "  make help                # Mostra este help"

.PHONY: infra-up infra-down
infra-up:
	@echo "🔧 Subindo infraestrutura..."
	cd $(INFRA_DIR) && $(COMPOSE) up -d

infra-down:
	@echo "⛔ Derrubando infraestrutura..."
	cd $(INFRA_DIR) && $(COMPOSE) down
.PHONY: dump-composer
dump-composer:
	@echo "Fazendo composer-dump ..."
	cd $(BACKEND_DIR) && $() composer dump-autoload

.PHONY: backend-install backend-start backend-test backend-analyze
backend-install:
	@echo "📦 Instalando dependências backend..."
	cd $(BACKEND_DIR) && composer install

backend-start:
	@echo "🚀 Iniciando servidor backend..."
	cd $(BACKEND_DIR) && php -S localhost:8001 -t public

backend-test:
	@echo "🧪 Executando testes backend (Kahlan)..."
	cd $(BACKEND_DIR) && vendor/bin/kahlan

backend-analyze:
	@echo "🔍 Executando PHPStan..."
	cd $(BACKEND_DIR) && vendor/bin/phpstan analyse src

.PHONY: frontend-install frontend-start frontend-test
frontend-install:
	@echo "📦 Instalando dependências frontend..."
	cd $(FRONTEND_DIR) && pnpm install

frontend-start:
	@echo "🚀 Iniciando servidor frontend..."
	cd $(FRONTEND_DIR) && pnpm dev

frontend-test:
	@echo "🧪 Executando testes frontend..."
	cd $(FRONTEND_DIR) && pnpm test

.PHONY: up down
up: infra-up backend-install frontend-install
	@echo "✅ Tudo pronto!"

down: infra-down
	@echo "✅ Infra parada!"
