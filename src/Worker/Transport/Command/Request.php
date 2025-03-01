<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Worker\Transport\Command;

use Temporal\DataConverter\EncodedValues;
use Temporal\DataConverter\ValuesInterface;

/**
 * Carries request to perform host action with payloads and failure as context. Can be cancelled if allows
 *
 * @psalm-import-type RequestOptions from RequestInterface
 */
class Request implements RequestInterface
{
    use RequestTrait;

    protected static int $lastID = 9000;
    protected int $id;
    protected ValuesInterface $payloads;
    protected ?\Throwable $failure = null;

    /**
     * @param non-empty-string $name
     * @param RequestOptions $options
     */
    public function __construct(
        protected string $name,
        protected array $options = [],
        ValuesInterface $payloads = null,
        protected ?object $header = null,
    ) {
        $this->payloads = $payloads ?? EncodedValues::empty();
        $this->id = $this->getNextID();
    }

    public function getID(): int
    {
        return $this->id;
    }

    public function setFailure(?\Throwable $failure): void
    {
        $this->failure = $failure;
    }

    public function getFailure(): ?\Throwable
    {
        return $this->failure;
    }

    private function getNextID(): int
    {
        $next = ++static::$lastID;

        if ($next >= \PHP_INT_MAX) {
            $next = static::$lastID = 1;
        }

        return $next;
    }
}
