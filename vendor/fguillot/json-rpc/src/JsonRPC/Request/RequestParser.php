<?php

namespace JsonRPC\Request;

use Exception;
use JsonRPC\Exception\AccessDeniedException;
use JsonRPC\Exception\AuthenticationFailureException;
use JsonRPC\Exception\InvalidJsonRpcFormatException;
use JsonRPC\ProcedureHandler;
use JsonRPC\Response\ResponseBuilder;
use JsonRPC\Validator\JsonFormatValidator;
use JsonRPC\Validator\RpcFormatValidator;

/**
 * Class RequestParser
 *
 * @package JsonRPC
 * @author  Frederic Guillot
 */
class RequestParser
{
    /**
     * Request payload
     *
     * @access protected
     * @var mixed
     */
    protected $payload;

    /**
     * ProcedureHandler
     *
     * @access protected
     * @var ProcedureHandler
     */
    protected $procedureHandler;

    /**
     * Get new object instance
     *
     * @static
     * @access public
     * @return RequestParser
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
     * Set procedure handler
     *
     * @access public
     * @param  ProcedureHandler $procedureHandler
     * @return $this
     */
    public function withProcedureHandler(ProcedureHandler $procedureHandler)
    {
        $this->procedureHandler = $procedureHandler;
        return $this;
    }

    /**
     * Parse incoming request
     *
     * @access public
     * @return string
     * @throws AccessDeniedException
     * @throws AuthenticationFailureException
     */
    public function parse()
    {
        try {

            JsonFormatValidator::validate($this->payload);
            RpcFormatValidator::validate($this->payload);

            $result = $this->procedureHandler->executeProcedure(
                $this->payload['method'],
                empty($this->payload['params']) ? array() : $this->payload['params']
            );

            if (! $this->isNotification()) {
                return ResponseBuilder::create()
                    ->withId($this->payload['id'])
                    ->withResult($result)
                    ->build();
            }
        } catch (Exception $e) {

            if ($e instanceof AccessDeniedException || $e instanceof AuthenticationFailureException) {
                throw $e;
            }

            if ($e instanceof InvalidJsonRpcFormatException || ! $this->isNotification()) {
                return ResponseBuilder::create()
                    ->withId(isset($this->payload['id']) ? $this->payload['id'] : null)
                    ->withException($e)
                    ->build();
            }
        }

        return '';
    }

    /**
     * Return true if the message is a notification
     *
     * @access private
     * @return bool
     */
    private function isNotification()
    {
        return is_array($this->payload) && !isset($this->payload['id']);
    }
}
