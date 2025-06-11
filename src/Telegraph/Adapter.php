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

    public function getListInfo(
        string $listId
    ): ?ListInfo;

    public function subscribe(
        SubscriptionRequest $request
    ): SubscriptionResponse;

    public function update(
        SubscriptionRequest $request
    ): SubscriptionResponse;

    public function unsubscribe(
        string $email
    ): SubscriptionResponse;

    public function getMemberInfo(
        string $email
    ): ?MemberInfo;
}
