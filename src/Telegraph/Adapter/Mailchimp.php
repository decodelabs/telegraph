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
}
