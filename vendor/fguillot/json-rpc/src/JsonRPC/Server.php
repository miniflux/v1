<?php

namespace JsonRPC;

use Closure;
use Exception;
use JsonRPC\Request\BatchRequestParser;
use JsonRPC\Request\RequestParser;
use JsonRPC\Response\ResponseBuilder;
use JsonRPC\Validator\HostValidator;
use JsonRPC\Validator\JsonFormatValidator;
use JsonRPC\Validator\UserValidator;

/**
 * JsonRPC server class
 *
 * @package JsonRPC
 * @author  Frederic Guillot
 */
class Server
{
    /**
     * Allowed hosts
     *
     * @access private
     * @var array
     */
    private $hosts = array();

    /**
     * Data received from the client
     *
     * @access private
     * @var array
     */
    private $payload = array();

    /**
     * List of exception classes that should be relayed to client
     *
     * @access private
     * @var array
     */
    private $exceptions = array();

    /**
     * Username
     *
     * @access private
     * @var string
     */
    private $username = '';

    /**
     * Password
     *
     * @access private
     * @var string
     */
    private $password = '';

    /**
     * Allowed users
     *
     * @access private
     * @var array
     */
    private $users = array();

    /**
     * $_SERVER
     *
     * @access private
     * @var array
     */
    private $serverVariable;

    /**
     * ProcedureHandler object
     *
     * @access private
     * @var ProcedureHandler
     */
    private $procedureHandler;

    /**
     * Constructor
     *
     * @access public
     * @param  string $request
     * @param array   $server
     */
    public function __construct($request = '', array $server = array())
    {
        if ($request !== '') {
            $this->payload = json_decode($request, true);
        } else {
            $this->payload = json_decode(file_get_contents('php://input'), true);
        }

        $this->serverVariable = $server ?: $_SERVER;
        $this->procedureHandler = new ProcedureHandler();
    }

    /**
     * Define alternative authentication header
     *
     * @access public
     * @param  string   $header   Header name
     * @return Server
     */
    public function setAuthenticationHeader($header)
    {
        if (! empty($header)) {
            $header = 'HTTP_'.str_replace('-', '_', strtoupper($header));
            $value = $this->getServerVariable($header);

            if (! empty($value)) {
                list($this->username, $this->password) = explode(':', base64_decode($value));
            }
        }

        return $this;
    }

    /**
     * Get ProcedureHandler
     *
     * @access public
     * @return ProcedureHandler
     */
    public function getProcedureHandler()
    {
        return $this->procedureHandler;
    }

    /**
     * Get username
     *
     * @access public
     * @return string
     */
    public function getUsername()
    {
        return $this->username ?: $this->getServerVariable('PHP_AUTH_USER');
    }

    /**
     * Get password
     *
     * @access public
     * @return string
     */
    public function getPassword()
    {
        return $this->password ?: $this->getServerVariable('PHP_AUTH_PW');
    }

    /**
     * IP based client restrictions
     *
     * @access public
     * @param  array   $hosts   List of hosts
     * @return Server
     */
    public function allowHosts(array $hosts)
    {
        $this->hosts = $hosts;
        return $this;
    }

    /**
     * HTTP Basic authentication
     *
     * @access public
     * @param  array   $users   Dictionary of username/password
     * @return Server
     */
    public function authentication(array $users)
    {
        $this->users = $users;
        return $this;
    }

    /**
     * Register a new procedure
     *
     * @access public
     * @param  string   $procedure       Procedure name
     * @param  closure  $callback        Callback
     * @return Server
     */
    public function register($procedure, Closure $callback)
    {
        $this->procedureHandler->withCallback($procedure, $callback);
        return $this;
    }

    /**
     * Bind a procedure to a class
     *
     * @access public
     * @param  string   $procedure    Procedure name
     * @param  mixed    $class        Class name or instance
     * @param  string   $method       Procedure name
     * @return Server
     */
    public function bind($procedure, $class, $method = '')
    {
        $this->procedureHandler->withClassAndMethod($procedure, $class, $method);
        return $this;
    }

    /**
     * Bind a class instance
     *
     * @access public
     * @param  mixed   $instance    Instance name
     * @return Server
     */
    public function attach($instance)
    {
        $this->procedureHandler->withObject($instance);
        return $this;
    }

    /**
     * Bind an exception
     * If this exception occurs it is relayed to the client as JSON-RPC error
     *
     * @access public
     * @param  mixed   $exception    Exception class. Defaults to all.
     * @return Server
     */
    public function attachException($exception = 'Exception')
    {
        $this->exceptions[] = $exception;
        return $this;
    }

    /**
     * Attach a method that will be called before the procedure
     *
     * @access public
     * @param  string  $before
     * @return Server
     */
    public function before($before)
    {
        $this->procedureHandler->withBeforeMethod($before);
        return $this;
    }

    /**
     * Parse incoming requests
     *
     * @access public
     * @return string
     */
    public function execute()
    {
        $responseBuilder = ResponseBuilder::create();

        try {
            $this->procedureHandler
                ->withUsername($this->getUsername())
                ->withPassword($this->getPassword());

            JsonFormatValidator::validate($this->payload);
            HostValidator::validate($this->hosts, $this->getServerVariable('REMOTE_ADDR'));
            UserValidator::validate($this->users, $this->getUsername(), $this->getPassword());

            $response = $this->parseRequest();

        } catch (Exception $e) {
            $response = $responseBuilder->withException($e)->build();
        }

        $responseBuilder->sendHeaders();
        return $response;
    }

    /**
     * Parse incoming request
     *
     * @access private
     * @return string
     */
    private function parseRequest()
    {
        if (BatchRequestParser::isBatchRequest($this->payload)) {
            return BatchRequestParser::create()
                ->withPayload($this->payload)
                ->withProcedureHandler($this->procedureHandler)
                ->parse();
        }

        return RequestParser::create()
            ->withPayload($this->payload)
            ->withProcedureHandler($this->procedureHandler)
            ->parse();
    }

    /**
     * Check existence and get value of server variable
     *
     * @access private
     * @param  string $variable
     * @return string|null
     */
    private function getServerVariable($variable)
    {
        return isset($this->serverVariable[$variable]) ? $this->serverVariable[$variable] : null;
    }
}
