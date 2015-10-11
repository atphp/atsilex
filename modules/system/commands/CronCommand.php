<?php

namespace atsilex\module\system\commands;

/**
 * @TODO: Cron command.
 */
class CronCommand extends AppAwareCmd
{
    const NAME        = 'at:cron';
    const DESCRIPTION = 'Run cron jobs.';
}
