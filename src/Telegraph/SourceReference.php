<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph;

class SourceReference
{
    public protected(set) string $name;
    public protected(set) string $remoteId;

    public function __construct(
        string $name,
        string $remoteId
    ) {
        $this->name = $name;
        $this->remoteId = $remoteId;
    }
}
