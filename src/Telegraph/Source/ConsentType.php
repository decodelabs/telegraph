<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph\Source;

enum ConsentType: string
{
    case Email = 'email';
    case Phone = 'phone';
    case Sms = 'sms';
    case Mail = 'mail';
    case Advertising = 'advertising';
    case Other = 'other';
}
