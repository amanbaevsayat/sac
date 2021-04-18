<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Throwable;

abstract class BaseException extends Exception implements LarabaseException
{
    private array $debug = [];

    private array $data = [];

    /**
     * BaseException constructor.
     * @param string $message
     * @param int $code
     * @param array $debug
     * @param Throwable|null $previous
     * @param array $data
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        array $debug = [],
        Throwable $previous = null,
        $data = []
    ) {
        parent::__construct($message, $code, $previous);
        $this->setDebug($debug);
        $this->setData($data);
    }

    /**
     * @return array
     */
    public function getDebug(): array
    {
        return $this->debug;
    }

    /**
     * @param array $debug
     */
    public function setDebug(array $debug = []): void
    {
        $this->debug = $debug;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data = []): void
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getDebugAsString(): string
    {
        return print_r($this->debug, true);
    }
}
