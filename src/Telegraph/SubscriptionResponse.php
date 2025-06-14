<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph;

use DecodeLabs\Relay\Mailbox;

class SubscriptionResponse
{
    public bool $success = false;
    public bool $subscribed = false;
    public bool $requiresManualInput = false;
    public ?string $manualInputUrl = null;

    public ?Mailbox $mailbox = null {
        set(string|Mailbox|null $value) {
            $this->mailbox = Mailbox::create($value);
        }
    }

    public bool $bounced = false;
    public bool $invalid = false;
    public bool $throttled = false;

    public function __construct(
        bool $success = false,
        bool $subscribed = false,
        bool $requiresManualInput = false,
        ?string $manualInputUrl = null,
        ?Mailbox $mailbox = null,
        bool $bounced = false,
        bool $invalid = false,
        bool $throttled = false
    ) {
        $this->success = $success;
        $this->subscribed = $subscribed;
        $this->requiresManualInput = $requiresManualInput;
        $this->manualInputUrl = $manualInputUrl;
        $this->mailbox = $mailbox;
        $this->bounced = $bounced;
        $this->invalid = $invalid;
        $this->throttled = $throttled;
    }
}
