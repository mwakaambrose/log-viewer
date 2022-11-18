<?php

use Illuminate\Support\Facades\File;
use App\LogFile;

beforeEach(function () {
    $this->file = generateLogFile();
    File::append($this->file->path, makeLogEntry());
});

afterEach(fn () => clearGeneratedLogFiles());

it('can scan a log file', function () {
    $logReader = $this->file->logs();
    expect($logReader->requiresScan())->toBeTrue();

    $logReader->scan();

    expect($logReader->requiresScan())->toBeFalse()
        ->and($logReader->index()->total())->toBe(1);
});

it('can re-scan the file after a new entry has been added', function () {
    $logReader = $this->file->logs();
    $logReader->scan();

    \Spatie\TestTime\TestTime::addMinute();

    File::append($this->file->path, PHP_EOL.makeLogEntry());

    // re-instantiate the log reader to make sure we don't have anything cached
    $this->file = new LogFile($this->file->name, $this->file->path);
    $logReader = $this->file->logs();
    expect($logReader->requiresScan())->toBeTrue();

    $logReader->scan();

    expect($logReader->requiresScan())->toBeFalse()
        ->and($logReader->index()->total())->toBe(2)
        ->and($logReader->index()->getFlatIndex())->toHaveCount(2);
});
