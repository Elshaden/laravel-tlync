<?php

namespace Elshaden\Tlync\Commands;

use Illuminate\Console\Command;

class TlyncCommand extends Command
{
    public $signature = 'laravel-tlync';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
