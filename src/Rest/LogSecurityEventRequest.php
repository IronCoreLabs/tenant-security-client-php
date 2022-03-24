<?php

declare(strict_types=1);

namespace IronCore\Rest;

use IronCore\EventMetadata;
use IronCore\IclFieldsWithEvent;
use IronCore\Rest\IronCoreRequest;
use IronCore\SecurityEvents\SecurityEvent;

/**
 * Request to the Tenant Security Proxy's security event logging endpoint
 */
class LogSecurityEventRequest extends IronCoreRequest
{
    /**
     * @var EventMetadata
     */
    private $metadata;
    /**
     * @var SecurityEvent
     */
    private $event;

    /**
     * @param EventMetadata $metadata Metadata about the security event that occurred
     * @param SecurityEvent $event The security event that occurred
     */
    public function __construct(EventMetadata $metadata, SecurityEvent $event)
    {
        $this->metadata = $metadata;
        $this->event = $event;
    }

    public function getPostData(): array
    {
        $postData = $this->metadata->getPostData();
        $postData["iclFields"] = new IclFieldsWithEvent($postData["iclFields"], $this->event);
        return $postData;
    }
}
