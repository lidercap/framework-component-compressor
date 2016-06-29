#########################
# CONFIGURATION SECTION #
#########################

BROWSER=google-chrome
BOLD=\033[1m
ENDBOLD=\033[0m
STDOUT=> /dev/null 2>&1
BIN=bin
BUILD=build
COMPOSER=/usr/bin/composer

NAME=`sed 's/[\", ]//g' composer.json | grep name | cut -d: -f2`
DESC=`sed 's/[\",]//g' composer.json | grep description | cut -d: -f2 | sed -e 's/^[ \t]*//'`
VERSION=`sed 's/[\", ]//g' composer.json | grep version | cut -d: -f2`

build: .clear .check-composer lint phpcs
	@[ -d ${BUILD} ] || mkdir ${BUILD}
	@make testdox > /dev/null
	@echo " - All tests passing"
	@echo ""
	@echo " - \\o/ BUILD SUCCESS!!!"
	@echo ""

install: .clear .check-composer
	@$(COMPOSER) install

lint: .clear
	@for file in `find ./src` ; do \
		results=`php -l $$file`; \
		if [ "$$results" != "No syntax errors detected in $$file" ]; then \
			echo $$results; \
			echo ""; \
			exit 1; \
		fi; \
	done;
	@echo " - No syntax errors detected"

phpcs:
	@$(BIN)/phpcs --standard=phpcs.xml src tests
	@echo " - No code standards violation detected"

phpmd: rw .clear
	@trap "${BIN}/phpmd --suffixes php ${SRC} html cleancode,codesize,controversial,design,naming,unusedcode --reportfile ${BUILD}/pmd.html" EXIT
	@echo " - Mess detector report generated"

test: .clear
	@$(BIN)/phpunit

testdox: .clear
	@$(BIN)/phpunit --testdox --coverage-html=${BUILD}/coverage
	@echo "\n\\o/ All tests passing!!!"
	@echo ""

coverage:
	@[ -d ${BUILD}/coverage ] || make testdox
	@$(BROWSER) ${BUILD}/coverage/index.html

clean:
	@echo "${BOLD}==> Removing build and temporary files...${ENDBOLD}"
	@rm -Rf ${BUILD} coverage.xml

clean-all: .clear clean
	@echo "${BOLD}==> Removing external dependencies...${ENDBOLD}"
	@rm -Rf ${BIN} vendor

.check-composer:
	@if [ ! -f ${COMPOSER} ]; then \
		echo "Composer faltando. Para instalar execute:"; \
		echo ""; \
		echo "  curl -sS https://getcomposer.org/installer | php"; \
		echo "  chmod 755 composer.phar"; \
		echo "  sudo mv composer.phar ${COMPOSER}"; \
		echo ""; \
		exit 1; \
	fi; \

.clear:
	@clear

help: .clear
	@echo "${DESC} (${NAME} - ${VERSION})"
	@echo "Uso: make [options]"
	@echo ""
	@echo "  build (default)    Build para distribuição"
	@echo "  install            Instala as externas dependências do projeto"
	@echo ""
	@echo "  lint               Executa a verificação de sintaxe"
	@echo "  phpcs              Executa a verificação de padrão de codificação"
	@echo "  test               Executa os testes unitários sem relatório"
	@echo "  testdox            Executa os testes unitários com relatório de cobertura"
	@echo "  coverage           Abre no navegador o relatório de cobertura"
	@echo ""
	@echo "  clean              Apaga os arquivos de build e temporários"
	@echo "  clean-all          Apaga arquivoas temporários, de build e dependências externas"
	@echo "  help               Exibe esta mensagem de HELP"
	@echo ""

.PHONY: build install lint test testdox coverage clean clean-all help
