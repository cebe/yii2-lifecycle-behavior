
example.png: example.dot
	dot -Tpng example.dot -o example.png

fix-style:
	vendor/bin/indent --tabs composer.json
	vendor/bin/indent --tabs src/LifecycleBehavior.php
	vendor/bin/indent --tabs src/StatusChangeNotAllowedException.php

install:
	composer install --prefer-dist --no-interaction

test:
	vendor/bin/phpunit

.PHONY: fix-style install test
