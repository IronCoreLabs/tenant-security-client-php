<?php

declare(strict_types=1);

namespace IronCore\Rest;

abstract class IronCoreRequest
{
    public function getJsonData()
    {
        return json_encode($this->getPostData(), JSON_FORCE_OBJECT);
    }

    abstract public function getPostData(): array;
}
