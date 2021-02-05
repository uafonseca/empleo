<?php

namespace Deployer;

require __DIR__ . './../vendor/deployer/deployer/recipe/symfony4.php';
require __DIR__ . './../vendor/deployer/recipes/recipe/slack.php';
require __DIR__ . './../vendor/deployer/recipes/recipe/yarn.php';


set('repository', 'git@github.com:roberto910907/empleo.ec.git');

set('application', 'serviciosyempleos');

host('beta')
    ->hostname('emplear.gessmac.com')
    ->set('branch', 'master')
    ->user('deploy')
    ->set('deploy_path', '/var/www/html/empleo');

host('production')
    ->hostname('serviciosyempleos.com')
    ->set('branch', 'master')
    ->user('deploy')
    ->set('deploy_path', '/var/www/html/empleo_prod');

set('composer_options', '{{composer_action}} --verbose --prefer-dist --no-progress --no-interaction --optimize-autoloader --no-suggest');

set('git_tty', false);

set('writable_mode', 'chmod');

set('writable_use_sudo', true);

set('writable_chmod_recursive',true);

set('shared_dirs', ['var/log', 'var/sessions', 'vendor', 'public/images', 'public/site/images', 'public/site/docs']);

set('writable_dirs', ['var/log','var/cache','var/sessions', 'public/','/var/www/html/empleo','/var/www/html/empleo_prod']);

set('ssh_multiplexing', true);

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

desc('chmod');
task('chmod:777', function () {
    run('sudo chmod -R 777 {{deploy_path}}/releases/{{release_name}}');
});


desc('Compile assets in production');
task('yarn:run:production', 'yarn run encore production');

desc('Database update');
task('database:update', function () {
    run('{{bin/console}} doctrine:schema:update --force');
});

desc('Publish assets');
task('assets:install', '{{bin/console}} assets:install --symlink public');


desc('Dumping js routes');
task('dump:js-routes', '{{bin/console}} fos:js-routing:dump --target=public/bundles/fosjsrouting/js/fos_js_routing.js');


task('build', [
//    'database:update',
    'assets:install',
    'dump:js-routes',
    'yarn:install',
    'yarn:run:production',
]);

after('deploy:vendors', 'build');
after('deploy:failed',  'deploy:unlock');
after('cleanup', 'chmod:777');





