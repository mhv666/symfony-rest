#! /usr/local/bin/php
<?php
require_once '../../vendor/autoload.php';
$exe1 = shell_exec('php /appdata/www/bin/console cache:clear --env=test');
$exe2 = shell_exec('php /appdata/www/bin/console  doctrine:database:drop --if-exists --force --env=test');
$exe3 = shell_exec('php /appdata/www/bin/console  doctrine:database:create --env=test');
$exe4  = shell_exec('php /appdata/www/bin/console  doctrine:migrations:migrate --env=test --no-interaction');
$exe5  = shell_exec('php /appdata/www/src/Scripts  ./loadData.php test');

//$exe6  = shell_exec('php /appdata/www/bin/phpunit  /appdata/www/tests/Controller/');
