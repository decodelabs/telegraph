<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph;

use DecodeLabs\Telegraph\Source\ListInfo;
use DecodeLabs\Telegraph\Source\MemberInfo;

interface Adapter
{
    /**
     * @param array<string,mixed> $settings
     */
    public function __construct(
        array $settings
    );

    public function fetchListInfo(
        string $listId
    ): ?ListInfo;

    public function subscribe(
        SubscriptionRequest $request
    ): SubscriptionResponse;

    public function update(
        SubscriptionRequest $request
    ): SubscriptionResponse;

    public function unsubscribe(
        string $listId,
        string $email
    ): SubscriptionResponse;

    public function fetchMemberInfo(
        string $listId,
        string $email
    ): ?MemberInfo;
}
