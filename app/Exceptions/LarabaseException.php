<?php

declare(strict_types=1);

namespace App\Exceptions;

interface LarabaseException
{
    public function getDebug(): array;

    public function setDebug(array $debug = []): void;

    public function getData(): array;

    public function setData(array $data = []): void;

    public function getDebugAsString(): string;
}
