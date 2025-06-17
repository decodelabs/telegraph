<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph;

use DecodeLabs\Telegraph\Source\MemberInfo;

class AdapterActionResult
{
    public SubscriptionResponse $response;
    public ?MemberInfo $memberInfo = null;

    public function __construct(
        SubscriptionResponse $response,
        ?MemberInfo $memberInfo = null
    ) {
        $this->response = $response;
        $this->memberInfo = $memberInfo;
    }
}
