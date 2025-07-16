<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph\Source;

enum MemberStatus: string
{
    case Subscribed = 'subscribed';
    case Pending = 'pending';
    case Unsubscribed = 'unsubscribed';
    case Invalid = 'invalid';
    case Archived = 'archived';
}
