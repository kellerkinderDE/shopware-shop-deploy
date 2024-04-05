<?php

declare(strict_types=1);

namespace Deployer;

require 'recipe/common.php';
require 'recipe/composer.php';
require 'contrib/rsync.php';
require 'contrib/cachetool.php';

set('writable_mode', 'chmod');
set('rsync', [
    'exclude' => [
        'config/jwt',
        'files/',
        'var/cache/',
        'var/log/',
        'public/media/',
        'public/sitemap',
        'public/thumbnail/',
        '.env',
        '.git',
        '.deployment/',
    ],
    'exclude-file'  => false,
    'include'       => [],
    'include-file'  => false,
    'filter'        => [],
    'filter-file'   => false,
    'filter-perdir' => false,
    'flags'         => 'rzEv',
    'options'       => [
        'delete',
        'links',
        'quiet',
    ],
    'timeout' => null,
]);

task('shopware6:check_installed', function (): void {
    if (!test('cd {{release_path}} && {{bin/php}} {{console}} system:is-installed > /dev/null 2>&1')) {
        throw new \RuntimeException('Shopware is not installed. Login to the server, navigate to the new `release/` folder, run `bin/console system:setup` (add any existing JWT keys to `shared/config/jwt`), import a database or run `bin/console system:install` and try again');
    }
});

task('shopware6:plugins:install_update', function (): void {
    run('cd {{release_path}} && {{bin/php}} {{console}} plugin:refresh');

    foreach (get('plugins') as $plugin) {
        run("cd {{release_path}} && {{bin/php}} {{console}} plugin:install {$plugin} --activate");
        run("cd {{release_path}} && {{bin/php}} {{console}} plugin:update {$plugin}");
    }

    run('cd {{release_path}} && {{bin/php}} {{console}} cache:clear');
});

task('shopware6:update', function (): void {
    run('cd {{release_path}} && [ ! -f vendor/autoload.php ] || {{bin/php}} {{console}} system:update:prepare');
    run('cd {{release_path}} && [ ! -f vendor/autoload.php ] || {{bin/php}} {{console}} system:update:finish --skip-asset-build');
});

task('shopware6:messenger:stop', function (): void {
    if (has('previous_release')) {
        run('cd {{previous_release}} && {{bin/php}} {{console}} messenger:stop-workers');
    }
});

task('shopware6:bundle:dump', function (): void {
    run('cd {{release_path}} && {{bin/php}} {{console}} bundle:dump');
});

task('shopware6:theme:compile', function (): void {
    run('cd {{release_path}} && {{bin/php}} {{console}} theme:dump');
    run('cd {{release_path}} && {{bin/php}} {{console}} asset:install');
    run('cd {{release_path}} && {{bin/php}} {{console}} theme:compile');
});

task('shopware6:scheduled-tasks', function (): void {
    run('cd {{release_path}} && {{bin/php}} {{console}} scheduled-task:register');
});
