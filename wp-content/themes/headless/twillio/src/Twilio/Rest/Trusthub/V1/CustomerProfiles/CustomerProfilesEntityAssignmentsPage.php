<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Trusthub\V1\CustomerProfiles;

use Twilio\Http\Response;
use Twilio\Page;
use Twilio\Version;

class CustomerProfilesEntityAssignmentsPage extends Page {
    /**
     * @param Version $version Version that contains the resource
     * @param Response $response Response from the API
     * @param array $solution The context solution
     */
    public function __construct(Version $version, Response $response, array $solution) {
        parent::__construct($version, $response);

        // Path Solution
        $this->solution = $solution;
    }

    /**
     * @param array $payload Payload response from the API
     * @return CustomerProfilesEntityAssignmentsInstance \Twilio\Rest\Trusthub\V1\CustomerProfiles\CustomerProfilesEntityAssignmentsInstance
     */
    public function buildInstance(array $payload): CustomerProfilesEntityAssignmentsInstance {
        return new CustomerProfilesEntityAssignmentsInstance(
            $this->version,
            $payload,
            $this->solution['customerProfileSid']
        );
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString(): string {
        return '[Twilio.Trusthub.V1.CustomerProfilesEntityAssignmentsPage]';
    }
}