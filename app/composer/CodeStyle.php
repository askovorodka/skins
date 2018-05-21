<?php

use Symfony\Component\Filesystem\Filesystem;
use Composer\Script\Event;

class CodeStyle
{
    public static function installPreCommitHook(Event $event)
    {
        $fs = new Filesystem();
        if (!$fs->exists(__DIR__.'/../../.git/hooks/pre-commit')) {
            $fs->copy(__DIR__.'/pre-commit', __DIR__.'/../../.git/hooks/pre-commit', true);
            $fs->chmod(__DIR__.'/../../.git/hooks/pre-commit', 0755);
            $event->getIO()->write('<info>The pre-commit git hook is instaled. CS fixed on commit</info>');
        }
    }
}
