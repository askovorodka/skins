<?php

namespace Deployer;
/**
 * dep deploy development - deploy to dev server
 * dep deploy --tag=1.0.0 production deploy tag to prod
 * dep deploy production deploy master to prod
 */
// Configuration
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

option('tag', 't', InputOption::VALUE_OPTIONAL, 'Tag to deploy');
option('branch', 'b', InputOption::VALUE_OPTIONAL, 'Branch to deploy', '{{default_branch}}');
argument('server', InputArgument::OPTIONAL, 'Run tasks only on this server or group of servers');

set('ssh_type', 'native');
set('ssh_multiplexing', true);

// Symfony build set
set('env', 'prod');

// Symfony shared dirs
set('shared_dirs', ['app/logs']);

// Symfony shared files
set('shared_files', ['app/config/parameters.yml']);

// Symfony writable dirs
set('writable_dirs', ['app/cache', 'app/logs']);

// Clear paths
set('clear_paths', ['web/app_*.php', 'web/config.php']);

// Symfony console opts
set('console_options', function () {
    $options = '--no-interaction --env={{env}}';

    return get('env') !== 'prod' ? $options : sprintf('%s --no-debug', $options);
});

set('repository', 'git@github.com:Cases4Real/Skins4Real.git');
set('git_path', '/home/skins4real/git');
set('src_path', '/home/skins4real/src');
set('bin/console', 'docker-compose run php ./bin/console');
set('git', 'GIT_WORK_TREE=../src/ git');

server('development', '207.154.237.9')
    ->user('skins4real')
    ->identityFile()
    ->set('default_branch', 'development')
;
server('production', '138.68.69.139')
    ->user('skins4real')
    ->identityFile()
    ->set('default_branch', 'master')
;

// Tasks
task('deploy:git', function() {
    cd('{{git_path}}');
    run('{{git}} fetch --tags');
    $tag = input()->getOption('tag');
    if (!empty($tag)) {
        run("{{git}} checkout tags/$tag");
    }
    else {
        run('{{git}} checkout {{default_branch}}');
        run('{{git}} pull origin {{default_branch}}');
    }
});

task('docker:cache:warmup', function () {
    cd('{{src_path}}');
    run('{{bin/console}} cache:warmup {{console_options}}');
})->desc('Warm up cache');

task('docker:cache:clear', function () {
    cd('{{src_path}}');
    run('{{bin/console}} cache:clear {{console_options}}');
});

task('docker:migrate', function () {
    cd('{{src_path}}');
   run('{{bin/console}} doctrine:migrations:migrate');
});

task('docker:composer:install', function () {
    cd('{{src_path}}');
    run('docker-compose run php composer install');
});

task('deploy', [
    'deploy:git',
    'docker:composer:install',
    'docker:migrate',
    'docker:cache:clear',
    'docker:cache:warmup'
])->desc('Deploy');

