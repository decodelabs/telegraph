<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph\Source;

enum EmailType: string
{
    case Html = 'html';
    case Text = 'text';
}
