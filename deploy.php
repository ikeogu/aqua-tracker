<?php
namespace Deployer;

require 'recipe/laravel.php';

// Config

set('repository', 'https://github.com/eachblock-io/aquatrack-api.git');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts

host('13.60.13.133')
    ->set('remote_user', 'deployer')
    ->set('deploy_path', '~/aqua-tracker');

// Hooks

after('deploy:failed', 'deploy:unlock');
