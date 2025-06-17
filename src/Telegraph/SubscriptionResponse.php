<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph;

use DecodeLabs\Relay\Mailbox;
use DecodeLabs\Telegraph\Source\MemberStatus;

class SubscriptionResponse
{
    public SourceReference $source;

    public bool $success = false;
    public ?FailureReason $failureReason = null;
    public ?string $manualInputUrl = null;

    public ?MemberStatus $status = null;
    public ?Mailbox $mailbox = null {
        set(string|Mailbox|null $value) {
            $this->mailbox = Mailbox::create($value);
        }
    }

    public bool $subscribed {
        get => $this->status === MemberStatus::Subscribed;
    }

    public bool $requiresManualInput {
        get => $this->manualInputUrl !== null;
    }

    public function __construct(
        SourceReference $source,
        bool $success = false,
        ?FailureReason $failureReason = null,
        ?string $manualInputUrl = null,
        ?MemberStatus $status = null,
        ?Mailbox $mailbox = null
    ) {
        $this->source = $source;

        $this->success = $success;
        $this->failureReason = $failureReason;
        $this->manualInputUrl = $manualInputUrl;

        $this->status = $status;
        $this->mailbox = $mailbox;
    }
}
