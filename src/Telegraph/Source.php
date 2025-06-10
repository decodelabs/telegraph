<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph;

class Source
{
    protected(set) string $name;
    protected(set) Adapter $adapter;
    protected(set) string $remoteId;

    public function __construct(
        string $name,
        Adapter $adapter,
        string $remoteId
    ) {
        $this->name = $name;
        $this->adapter = $adapter;
        $this->remoteId = $remoteId;
    }
}
