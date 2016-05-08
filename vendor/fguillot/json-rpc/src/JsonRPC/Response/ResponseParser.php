<?php

namespace JsonRPC\Response;

use BadFunctionCallException;
use InvalidArgumentException;
use JsonRPC\Exception\InvalidJsonFormatException;
use JsonRPC\Exception\InvalidJsonRpcFormatException;
use JsonRPC\Exception\ResponseException;
use JsonRPC\Validator\JsonFormatValidator;

/**
 * Class ResponseParser
 *
 * @package JsonRPC\Request
 * @author  Frederic Guillot
 */
class ResponseParser
{
    /**
     * Payload
     *
     * @access private
     * @var mixed
     */
    private $payload;

    /**
     * Get new object instance
     *
     * @static
     * @access public
     * @return ResponseParser
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Set payload
     *
     * @access public
     * @param  mixed $payload
     * @return $this
     */
    public function withPayload($payload)
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * Parse response
     *
     * @access public
     * @throws InvalidJsonFormatException
     * @throws InvalidJsonRpcFormatException
     * @throws ResponseException
     * @return mixed
     */
    public function parse()
    {
        JsonFormatValidator::validate($this->payload);

        if ($this->isBatchResponse()) {
            $results = array();

            foreach ($this->payload as $response) {
                $results[] = self::create()
                    ->withPayload($response)
                    ->parse();
            }

            return $results;
        }

        if (isset($this->payload['error']['code'])) {
            $this->handleExceptions();
        }

        return isset($this->payload['result']) ? $this->payload['result'] : null;
    }

    /**
     * Handle exceptions
     *
     * @access private
     * @throws InvalidJsonFormatException
     * @throws InvalidJsonRpcFormatException
     * @throws ResponseException
     */
    private function handleExceptions()
    {
        switch ($this->payload['error']['code']) {
            case -32700:
                throw new InvalidJsonFormatException('Parse error: '.$this->payload['error']['message']);
            case -32600:
                throw new InvalidJsonRpcFormatException('Invalid Request: '.$this->payload['error']['message']);
            case -32601:
                throw new BadFunctionCallException('Procedure not found: '.$this->payload['error']['message']);
            case -32602:
                throw new InvalidArgumentException('Invalid arguments: '.$this->payload['error']['message']);
            default:
                throw new ResponseException(
                    $this->payload['error']['message'],
                    $this->payload['error']['code'],
                    null,
                    isset($this->payload['error']['data']) ? $this->payload['error']['data'] : null
                );
        }
    }

    /**
     * Return true if we have a batch response
     *
     * @access private
     * @return boolean
     */
    private function isBatchResponse()
    {
        return array_keys($this->payload) === range(0, count($this->payload) - 1);
    }
}
