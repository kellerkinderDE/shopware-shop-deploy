<?php

declare(strict_types=1);

namespace Deployer;

require 'deploy-shopware6.php';

set('source_directory', '../');
add('shared_files', [
    '.env',
    'public/.htaccess',
    'install.lock',
]);
add('executable_files', [get('console')]);
add('shared_dirs', [
    'config/jwt',
    'files',
    'var/log',
    'public/media',
    'public/sitemap',
    'public/thumbnail',
]);
add('create_shared_dirs', [
    'config/jwt',
    'files',
    'var/cache',
    'var/log',
    'public/media',
    'public/sitemap',
    'public/thumbnail',
]);
add('writable_dirs', [
    'var/cache',
    'var/log',
    'files',
    'public',
]);
set('allow_anonymous_stats', false);
set('timing', new \DateTime());
set('default_timeout', 600);
set('keep_releases', 5);

// List of plugins to be installed/updated automatically
set('plugins', [
    'PluginName',
    'AnotherPluginName'
]);

after('deploy:failed', 'deploy:unlock');

import('deploy.yaml');
