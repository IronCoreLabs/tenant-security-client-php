<?php

declare(strict_types=1);

namespace IronCore;

use IronCore\SecurityEvents\SecurityEvent;

/**
 * Holds the event to pass to the Tenant Security Proxy for logging purposes.
 */
class IclFieldsWithEvent extends IclFields
{
    /**
     * @var string Security event to log when calling `logSecurityEvent`
     */
    public $event;

    public function __construct(
        IclFields $iclFields,
        SecurityEvent $event
    ) {
        $this->requestingId = $iclFields->requestingId;
        $this->dataLabel = $iclFields->dataLabel;
        $this->sourceIp = $iclFields->sourceIp;
        $this->objectId = $iclFields->objectId;
        $this->requestId = $iclFields->requestId;
        $this->event = $event->getFlatEvent();
    }
}
