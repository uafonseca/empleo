<?php

namespace Deployer;

require 'recipe/symfony.php';

inventory('hosts.yml');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

set('release_name', function () {
    return date('YmdHis');
});

// Tasks


