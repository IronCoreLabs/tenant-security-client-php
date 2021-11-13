<?php

declare(strict_types=1);

namespace IronCore\Rest;

/**
 * Abstract class representing a Tenant Security Proxy request object.
 */
abstract class IronCoreRequest
{
    /**
     * Gets the JSON-encoded form of the request payload.
     *
     * @return string JSON-encoded form of the request payload
     */
    final public function getJsonData(): string
    {
        return json_encode($this->getPostData(), JSON_FORCE_OBJECT);
    }

    /**
     * Method to get the request data as an associative array.
     *
     * @return array Associative array containing all of the object's fields
     */
    abstract public function getPostData(): array;
}
