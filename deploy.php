<?php
namespace Deployer;

(new \Symfony\Component\Dotenv\Dotenv())->load('.env');
array_map(function ($var) { set($var, getenv($var)); }, explode(',', $_SERVER['SYMFONY_DOTENV_VARS']));

require 'recipe/symfony4.php';

// Project name
set('application', 'Extranet');

// Project repository
set('repository', 'git@github.com:ikerib/extranet.git');

set('http_user', 'www-data');
set('shared_files', [
    '.env',
]);

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

set('shared_dirs', [
    'var/log',
    'var/sessions',
    'EXTRANET',
    'public/export'
]);

set('writable_dirs', [
    'var',
    'EXTRANET',
    'public/export'
]);

set('allow_anonymous_stats', false);
//
//set('env', [
//    'APP_FOLDER_PATH'   => 'EXTRANET',
//    'APP_ENV'           => 'prod'
//]);

// Hosts
host('172.23.64.36')
    ->user('root')
    ->set('branch', 'master')
    ->set('deploy_path', '/var/www/extranet');

// Tasks

task('build', function () {
    run('cd {{release_path}} && build');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.
#before('deploy:symlink', 'database:migrate');

set('bin/yarn', function () {
    return run('which yarn');
});
desc('Install Yarn packages');
task('yarn:install', function () {
    if (has('previous_release')) {
        if (test('[ -d {{previous_release}}/node_modules ]')) {
            run('cp -R {{previous_release}}/node_modules {{release_path}}');
        }
    }
    run("cd {{release_path}} && {{bin/yarn}}");
});

desc('Build my assets');
task('yarn:build', function () {
    if (has('previous_release')) {
        if (test('[ -d {{previous_release}}/node_modules ]')) {
            run('cp -R {{previous_release}}/node_modules {{release_path}}');
        }
    }
    run("cd {{release_path}} && {{bin/yarn}} build");
});

after( 'deploy:symlink', 'yarn:install' );
after( 'yarn:install', 'yarn:build' );
