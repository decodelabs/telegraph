<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph;

interface Adapter
{
    /**
     * @param array<string,mixed> $settings
     */
    public function __construct(
        array $settings
    );
}
