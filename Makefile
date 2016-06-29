#########################
# CONFIGURATION SECTION #
#########################

BROWSER=google-chrome
BOLD=\033[1m
ENDBOLD=\033[0m
STDOUT=> /dev/null 2>&1
BIN=bin
COMPOSER=${BIN}/composer.phar

NAME=`sed 's/[\", ]//g' composer.json | grep name | cut -d: -f2`
DESC=`sed 's/[\",]//g' composer.json | grep description | cut -d: -f2 | sed -e 's/^[ \t]*//'`
VERSION=`sed 's/[\", ]//g' composer.json | grep version | cut -d: -f2`

build: .clear .composer
	@echo "Building ${NAME}..."
	@rm -Rf build ; mkdir build
	@cp -Rf composer.* src vendor build
	@cd build && composer install --no-dev -o ${STDOUT}

install: .clear .composer
	@$(COMPOSER) install

lint:
	@$(BIN)/phpcs --standard=phpcs.xml src tests

test: .clear
	@$(BIN)/phpunit

testdox: .clear lint
	@$(BIN)/phpunit --testdox --coverage-html=coverage
	@echo "\n\\o/ All tests passing!!!"

coverage: testdox
	@$(BROWSER) coverage/index.html

clean:
	@echo "${BOLD}==> Removing build and temporary files...${ENDBOLD}"
	@rm -Rf build coverage coverage.xml

clean-all: .clear clean
	@echo "${BOLD}==> Removing external dependencies...${ENDBOLD}"
	@rm -Rf ${BIN} vendor

.composer:
	@if [ ! -f ${BIN}/composer.phar ]; then \
		curl -sS https://getcomposer.org/installer | php -- --install-dir=${BIN}; \
		chmod 755 ${COMPOSER}; \
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
	@echo "  test               Executa os testes unitários sem relatório"
	@echo "  testdox            Executa os testes unitários com relatório de cobertura"
	@echo "  coverage           Abre no navegador o relatório de cobertura"
	@echo ""
	@echo "  clean              Apaga os arquivos de build e temporários"
	@echo "  clean-all          Apaga arquivoas temporários, de build e dependências externas"
	@echo "  help               Exibe esta mensagem de HELP"
	@echo ""

.PHONY: build install lint test testdox coverage clean clean-all help
