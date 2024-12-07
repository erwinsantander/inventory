<?php

/**
 * This code was generated by
 * ___ _ _ _ _ _    _ ____    ____ ____ _    ____ ____ _  _ ____ ____ ____ ___ __   __
 *  |  | | | | |    | |  | __ |  | |__| | __ | __ |___ |\ | |___ |__/ |__|  | |  | |__/
 *  |  |_|_| | |___ | |__|    |__| |  | |    |__] |___ | \| |___ |  \ |  |  | |__| |  \
 *
 * Twilio - Events
 * This is the public Twilio REST API.
 *
 * NOTE: This class is auto generated by OpenAPI Generator.
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace Twilio\Rest\Events\V1;

use Twilio\ListResource;
use Twilio\Version;


class SchemaList extends ListResource
    {
    /**
     * Construct the SchemaList
     *
     * @param Version $version Version that contains the resource
     */
    public function __construct(
        Version $version
    ) {
        parent::__construct($version);

        // Path Solution
        $this->solution = [
        ];
    }

    /**
     * Constructs a SchemaContext
     *
     * @param string $id The unique identifier of the schema. Each schema can have multiple versions, that share the same id.
     */
    public function getContext(
        string $id
        
    ): SchemaContext
    {
        return new SchemaContext(
            $this->version,
            $id
        );
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString(): string
    {
        return '[Twilio.Events.V1.SchemaList]';
    }
}
