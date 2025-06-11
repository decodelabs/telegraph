<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph\Adapter;

use DecodeLabs\Coercion;
use DecodeLabs\Exceptional;
use DecodeLabs\Nuance\SensitiveProperty;
use DecodeLabs\Telegraph\Adapter;
use DecodeLabs\Telegraph\Source\ListInfo;
use DecodeLabs\Telegraph\Source\MemberInfo;
use DecodeLabs\Telegraph\SubscriptionRequest;
use DecodeLabs\Telegraph\SubscriptionResponse;

class Mailchimp implements Adapter
{
    #[SensitiveProperty]
    protected string $apiKey;

    public function __construct(
        array $settings
    ) {
        // API Key
        if(null === ($apiKey = Coercion::tryString($settings['apiKey'] ?? null))) {
            throw Exceptional::InvalidArgument(
                'Mailchimp API key is required'
            );
        }

        $this->apiKey = $apiKey;
    }

    public function getListInfo(
        string $listId
    ): ?ListInfo {
        return null;
    }

    public function subscribe(
        SubscriptionRequest $request
    ): SubscriptionResponse {
        return new SubscriptionResponse();
    }

    public function update(
        SubscriptionRequest $request
    ): SubscriptionResponse {
        return new SubscriptionResponse();
    }

    public function unsubscribe(
        string $email
    ): SubscriptionResponse {
        return new SubscriptionResponse();
    }

    public function getMemberInfo(
        string $email
    ): ?MemberInfo {
        return null;
    }
}
