<?php

namespace Deployer;

require __DIR__ . './../vendor/deployer/deployer/recipe/symfony4.php';
require __DIR__ . './../vendor/deployer/recipes/recipe/slack.php';
require __DIR__ . './../vendor/deployer/recipes/recipe/yarn.php';

inventory('hosts.yml');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', false);

set('shared_dirs', ['var/log', 'var/sessions', 'vendor', 'public/images', 'public/site/images', 'public/site/docs']);
set('writable_dirs', ['var','var/cache',  'public/images', 'public/site']);
set('writable_mode', 'chmod');

set('release_name', function () {
    return date('YmdHis');
});

set('keep_releases', 3);

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

set('slack_webhook', 'https://hooks.slack.com/services/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');
set('slack_text', "_{{user}}_ is deploying `{{ release_version_text }}` to *{{target}}*");
set('slack_success_text', '_{{user}}_ - Deploy `{{ release_version_text }}` to *{{target}}* successful');
set('slack_failure_text', '_{{user}}_ - Deploy `{{ release_version_text }}` to *{{target}}* failed');



// Set to false to make npm recipe dont't copy node_modules from previous release
set('previous_release', false);

//set('ssh_multiplexing', false);
//set('use_relative_symlinks', false);

desc('Compile assets in production');
task('yarn:run:production', 'yarn run encore production');

desc('Database update');
task('database:update', function () {
    run('{{bin/console}} doctrine:schema:update --force');
});

desc('Dumping js routes');
task('dump:js-routes', '{{bin/console}} fos:js-routing:dump --target=public/js/fos_js_routes.js');

task('build', [
    'database:update',
    'dump:js-routes',
    'yarn:install',
    'yarn:run:production',
]);

after('deploy:vendors', 'build');

