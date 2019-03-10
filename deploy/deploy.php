<?php

namespace Deployer;

require 'recipe/symfony4.php';

inventory('hosts.yml');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

set('shared_dirs', ['var/log', 'var/sessions', 'vendor']);

set('release_name', function () {
    return date('YmdHis');
});

set('env', [
    'APP_ENV' => 'production',
]);

desc('Update database schema');
task('deploy:schema:update', '{{bin/console}} doctrine:schema:update --force');

before('success', 'deploy:schema:update');
after('deploy:failed', 'deploy:unlock');


