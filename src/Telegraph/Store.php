<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph;

use DecodeLabs\Telegraph\Source\ListInfo;
use DecodeLabs\Telegraph\Source\MemberInfo;

interface Store
{
    public function storeListInfo(
        SourceReference $source,
        ListInfo $list
    ): void;

    public function fetchListInfo(
        SourceReference $source
    ): ?ListInfo;

    public function clearListInfo(
        SourceReference $source
    ): void;

    public function storeMemberInfo(
        SourceReference $source,
        string $userId,
        MemberInfo $member
    ): void;

    public function fetchMemberInfo(
        SourceReference $source,
        string $userId
    ): ?MemberInfo;

    public function clearMemberInfo(
        SourceReference $source,
        string $userId
    ): void;
}
