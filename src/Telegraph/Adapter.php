<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph;

use DecodeLabs\Telegraph\Source\ListInfo;
use DecodeLabs\Telegraph\Source\ListReference;
use DecodeLabs\Telegraph\Source\MemberInfo;

interface Adapter
{
    /**
     * @param array<string,mixed> $settings
     */
    public function __construct(
        array $settings
    );

    /**
     * @return array<ListReference>
     */
    public function fetchAllListReferences(): array;

    public function fetchListInfo(
        SourceReference $source,
    ): ?ListInfo;

    public function subscribe(
        SourceReference $source,
        ListInfo $listInfo,
        MemberDataRequest $request
    ): AdapterActionResult;

    public function update(
        SourceReference $source,
        ListInfo $listInfo,
        string $email,
        MemberDataRequest $request
    ): AdapterActionResult;

    public function unsubscribe(
        SourceReference $source,
        ListInfo $listInfo,
        string $email
    ): AdapterActionResult;

    public function fetchMemberInfo(
        SourceReference $source,
        ListInfo $listInfo,
        string $email,
    ): ?MemberInfo;
}
