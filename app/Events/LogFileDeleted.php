<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use App\LogFile;

class LogFileDeleted
{
    use Dispatchable;

    public function __construct(
        public LogFile $file
    ) {
    }
}
