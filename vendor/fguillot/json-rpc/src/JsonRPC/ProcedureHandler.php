<?php

namespace JsonRPC;

use BadFunctionCallException;
use Closure;
use InvalidArgumentException;
use ReflectionFunction;
use ReflectionMethod;

/**
 * Class ProcedureHandler
 *
 * @package JsonRPC
 * @author  Frederic Guillot
 */
class ProcedureHandler
{
    /**
     * List of procedures
     *
     * @access private
     * @var array
     */
    private $callbacks = array();

    /**
     * List of classes
     *
     * @access private
     * @var array
     */
    private $classes = array();

    /**
     * List of instances
     *
     * @access private
     * @var array
     */
    private $instances = array();

    /**
     * Method name to execute before the procedure
     *
     * @access private
     * @var string
     */
    private $before = '';

    /**
     * Username
     *
     * @access private
     * @var string
     */
    private $username;

    /**
     * Password
     *
     * @access private
     * @var string
     */
    private $password;

    /**
     * Set username
     *
     * @access public
     * @param  string $username
     * @return $this
     */
    public function withUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Set password
     *
     * @access public
     * @param  string $password
     * @return $this
     */
    public function withPassword($password)
    {
        $this->password = $password;
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
    public function withCallback($procedure, Closure $callback)
    {
        $this->callbacks[$procedure] = $callback;
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
    public function withClassAndMethod($procedure, $class, $method = '')
    {
        if ($method === '') {
            $method = $procedure;
        }

        $this->classes[$procedure] = array($class, $method);
        return $this;
    }

    /**
     * Bind a class instance
     *
     * @access public
     * @param  mixed   $instance
     * @return Server
     */
    public function withObject($instance)
    {
        $this->instances[] = $instance;
        return $this;
    }

    /**
     * Attach a method that will be called before the procedure
     *
     * @access public
     * @param  string  $before
     * @return Server
     */
    public function withBeforeMethod($before)
    {
        $this->before = $before;
        return $this;
    }

    /**
     * Execute the procedure
     *
     * @access public
     * @param  string   $procedure    Procedure name
     * @param  array    $params       Procedure params
     * @return mixed
     */
    public function executeProcedure($procedure, array $params = array())
    {
        if (isset($this->callbacks[$procedure])) {
            return $this->executeCallback($this->callbacks[$procedure], $params);
        }
        else if (isset($this->classes[$procedure]) && method_exists($this->classes[$procedure][0], $this->classes[$procedure][1])) {
            return $this->executeMethod($this->classes[$procedure][0], $this->classes[$procedure][1], $params);
        }

        foreach ($this->instances as $instance) {
            if (method_exists($instance, $procedure)) {
                return $this->executeMethod($instance, $procedure, $params);
            }
        }

        throw new BadFunctionCallException('Unable to find the procedure');
    }

    /**
     * Execute a callback
     *
     * @access public
     * @param  Closure   $callback     Callback
     * @param  array     $params       Procedure params
     * @return mixed
     */
    public function executeCallback(Closure $callback, $params)
    {
        $reflection = new ReflectionFunction($callback);

        $arguments = $this->getArguments(
            $params,
            $reflection->getParameters(),
            $reflection->getNumberOfRequiredParameters(),
            $reflection->getNumberOfParameters()
        );

        return $reflection->invokeArgs($arguments);
    }

    /**
     * Execute a method
     *
     * @access public
     * @param  mixed     $class        Class name or instance
     * @param  string    $method       Method name
     * @param  array     $params       Procedure params
     * @return mixed
     */
    public function executeMethod($class, $method, $params)
    {
        $instance = is_string($class) ? new $class : $class;

        // Execute before action
        if (! empty($this->before)) {
            if (is_callable($this->before)) {
                call_user_func_array($this->before, array($this->username, $this->password, get_class($class), $method));
            }
            else if (method_exists($instance, $this->before)) {
                $instance->{$this->before}($this->username, $this->password, get_class($class), $method);
            }
        }

        $reflection = new ReflectionMethod($class, $method);

        $arguments = $this->getArguments(
            $params,
            $reflection->getParameters(),
            $reflection->getNumberOfRequiredParameters(),
            $reflection->getNumberOfParameters()
        );

        return $reflection->invokeArgs($instance, $arguments);
    }

    /**
     * Get procedure arguments
     *
     * @access public
     * @param  array    $request_params       Incoming arguments
     * @param  array    $method_params        Procedure arguments
     * @param  integer  $nb_required_params   Number of required parameters
     * @param  integer  $nb_max_params        Maximum number of parameters
     * @return array
     */
    public function getArguments(array $request_params, array $method_params, $nb_required_params, $nb_max_params)
    {
        $nb_params = count($request_params);

        if ($nb_params < $nb_required_params) {
            throw new InvalidArgumentException('Wrong number of arguments');
        }

        if ($nb_params > $nb_max_params) {
            throw new InvalidArgumentException('Too many arguments');
        }

        if ($this->isPositionalArguments($request_params)) {
            return $request_params;
        }

        return $this->getNamedArguments($request_params, $method_params);
    }

    /**
     * Return true if we have positional parameters
     *
     * @access public
     * @param  array    $request_params      Incoming arguments
     * @return bool
     */
    public function isPositionalArguments(array $request_params)
    {
        return array_keys($request_params) === range(0, count($request_params) - 1);
    }

    /**
     * Get named arguments
     *
     * @access public
     * @param  array    $request_params      Incoming arguments
     * @param  array    $method_params       Procedure arguments
     * @return array
     */
    public function getNamedArguments(array $request_params, array $method_params)
    {
        $params = array();

        foreach ($method_params as $p) {
            $name = $p->getName();

            if (isset($request_params[$name])) {
                $params[$name] = $request_params[$name];
            }
            else if ($p->isDefaultValueAvailable()) {
                $params[$name] = $p->getDefaultValue();
            }
            else {
                throw new InvalidArgumentException('Missing argument: '.$name);
            }
        }

        return $params;
    }
}
