<?php

namespace App\Http\Controllers;

use App\Facades\LogViewer;
use App\LogFile;
use App\LogReader;

class ScanFilesController
{
    public function __invoke()
    {
        $files = LogViewer::getFiles();
        $filesRequiringScans = $files->filter(fn (LogFile $file) => $file->logs()->requiresScan());

        $filesRequiringScans->each(function (LogFile $file) {
            $file->logs()->scan();

            LogReader::clearInstance($file);
        });

        return response()->json([
            'success' => true,
        ]);
    }
}
