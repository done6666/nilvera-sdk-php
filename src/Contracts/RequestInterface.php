<?php

declare(strict_types=1);

namespace Nilvera\Contracts;

interface RequestInterface
{
    /** Serialize the DTO to an associative array ready to be sent as a JSON request body. */
    public function toArray(): array;
}
