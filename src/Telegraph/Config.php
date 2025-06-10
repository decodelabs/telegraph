<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph;

interface Config
{
    public function getDefaultSourceName(): ?string;

    public function getSourceAdapter(
        string $name
    ): ?string;

    public function getSourceRemoteId(
        string $name
    ): ?string;

    /**
     * @return array<string,mixed>
     */
    public function getSourceSettings(
        string $name
    ): array;
}
