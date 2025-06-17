<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph;

enum FailureReason
{
    case EmailInvalid;
    case Throttled;
    case Compliance;
    case ServiceUnavailable;
}
