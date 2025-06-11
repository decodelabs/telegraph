<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph\Source;

enum MemberStatus
{
    case Subscribed;
    case Pending;
    case Unsubscribed;
    case Invalid;
    case Archived;
}
