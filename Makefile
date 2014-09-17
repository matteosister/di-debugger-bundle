test:
	bin/behat --format=progress
	bin/phpspec run
behat:
	bin/behat --format=progress
phpspec:
	bin/phpspec run
