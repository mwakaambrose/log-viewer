<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use App\Facades\LogViewer;

class DownloadFileController
{
    public function __invoke(string $fileIdentifier)
    {
        LogViewer::auth();

        $file = LogViewer::getFile($fileIdentifier);

        abort_if(is_null($file), 404);

        Gate::authorize('downloadLogFile', $file);

        return $file->download();
    }
}
