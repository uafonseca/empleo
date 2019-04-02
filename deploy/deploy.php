<?php

namespace Deployer;

require __DIR__ . './../vendor/deployer/deployer/recipe/symfony4.php';
require __DIR__ . './../vendor/deployer/recipes/recipe/slack.php';

inventory('hosts.yml');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

set('shared_dirs', ['var/log', 'var/sessions', 'vendor']);
set('writable_dirs', ['var', 'public/images', 'public/site']);
set('writableusesudo', true);

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

set('slack_webhook', 'https://hooks.slack.com/services/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');
set('slack_text', "_{{user}}_ is deploying `{{ release_version_text }}` to *{{target}}*");
set('slack_success_text', '_{{user}}_ - Deploy `{{ release_version_text }}` to *{{target}}* successful');
set('slack_failure_text', '_{{user}}_ - Deploy `{{ release_version_text }}` to *{{target}}* failed');


desc('Update database schema');
task('deploy:schema:update', '{{bin/console}} doctrine:schema:update --force');

before('success', 'deploy:schema:update');
after('deploy:failed', 'deploy:unlock');

before('deploy', 'slack:notify');
after('success', 'slack:notify:success');
after('deploy:failed', 'slack:notify:failure');

