<?php

namespace Deployer;

require __DIR__ . './../vendor/deployer/deployer/recipe/symfony4.php';
require __DIR__ . './../vendor/deployer/recipes/recipe/slack.php';
require __DIR__ . './../vendor/deployer/recipes/recipe/yarn.php';

inventory('hosts.yml');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', false);

set('shared_dirs', ['var/log', 'var/sessions', 'vendor', 'public/images', 'public/site/images', 'public/site/docs']);
set('writable_dirs', ['var', 'public/images', 'public/site']);
set('writable_mode', 'chmod');

set('release_name', function () {
    return date('YmdHis');
});

set('keep_releases', 2);

set('env', [
    'APP_ENV' => 'production',
]);

set('release_version_text', function () {
    $release = get('branch');
    if (input()->hasOption('tag') && !empty(input()->getOption('tag'))) {
        $release = input()->getOption('tag');
    }
    return $release;
});


task('yarn:build', 'cd {{ deploy_path }}/current && yarn run build');

desc('Database update');
task('database:update', function () {
    run('{{bin/console}} doctrine:schema:update --force');
});

desc('Publish assets');
task('assets:install', '{{bin/console}} assets:install --symlink public');


desc('Dumping js routes');
task('dump:js-routes', '{{bin/console}} fos:js-routing:dump --target=public/bundles/fosjsrouting/js/fos_js_routing.js');

task('grant:permissions', 'sudo chmod -R 777 /var/www/html/Emplear');

task('build', [
    'database:update',
    'assets:install',
    'dump:js-routes',
    'yarn:install',
    'yarn:build',
    'grant:permissions',
]);

after('deploy:vendors', 'build');





