<?php

namespace JsonRPC\Request;

/**
 * Class BatchRequestParser
 *
 * @package JsonRPC\Request
 * @author  Frederic Guillot
 */
class BatchRequestParser extends RequestParser
{
    /**
     * Parse incoming request
     *
     * @access public
     * @return string
     */
    public function parse()
    {
        $responses = array();

        foreach ($this->payload as $payload) {
            $responses[] = RequestParser::create()
                ->withPayload($payload)
                ->withProcedureHandler($this->procedureHandler)
                ->parse();
        }

        $responses = array_filter($responses);
        return empty($responses) ? '' : '['.implode(',', $responses).']';
    }

    /**
     * Return true if we have a batch request
     *
     * @static
     * @access public
     * @param  array $payload
     * @return bool
     */
    public static function isBatchRequest(array $payload)
    {
        return is_array($payload) && array_keys($payload) === range(0, count($payload) - 1);
    }
}
