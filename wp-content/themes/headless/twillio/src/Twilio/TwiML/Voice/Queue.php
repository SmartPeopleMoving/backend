<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\TwiML\Voice;

use Twilio\TwiML\TwiML;

class Queue extends TwiML {
    /**
     * Queue constructor.
     *
     * @param string $name Queue name
     * @param array $attributes Optional attributes
     */
    public function __construct($name, $attributes = []) {
        parent::__construct('Queue', $name, $attributes);
    }

    /**
     * Add Url attribute.
     *
     * @param string $url Action URL
     */
    public function setUrl($url): self {
        return $this->setAttribute('url', $url);
    }

    /**
     * Add Method attribute.
     *
     * @param string $method Action URL method
     */
    public function setMethod($method): self {
        return $this->setAttribute('method', $method);
    }

    /**
     * Add ReservationSid attribute.
     *
     * @param string $reservationSid TaskRouter Reservation SID
     */
    public function setReservationSid($reservationSid): self {
        return $this->setAttribute('reservationSid', $reservationSid);
    }

    /**
     * Add PostWorkActivitySid attribute.
     *
     * @param string $postWorkActivitySid TaskRouter Activity SID
     */
    public function setPostWorkActivitySid($postWorkActivitySid): self {
        return $this->setAttribute('postWorkActivitySid', $postWorkActivitySid);
    }
}