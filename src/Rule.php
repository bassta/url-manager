<?php
/**
 * url_manager
 *
 * Copyright (c) 2016 Vasily Stashko
 */

namespace UrlManager;

/**
 * Class Rule
 * @package UrlManager
 */
class Rule
{
    /** @var string $method */
    protected $method;

    /** @var string $pattern */
    protected $pattern;

    /** @var string $route */
    protected $route;

    /**
     * Rule constructor.
     * @param string|array $method
     * @param string $pattern
     * @param string $route
     */
    public function __construct($method, $pattern, $route)
    {
        $this->method  = (array)$method;
        $this->pattern = $pattern;
        $this->route   = $route;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string|array $method
     * @return Rule
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param string $pattern
     * @return Rule
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
        return $this;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param string $route
     * @return Rule
     */
    public function setRoute($route)
    {
        $this->route = $route;
        return $this;
    }

    /**
     * Compares two rules.
     *
     * @param Rule $rule1
     * @param Rule $rule2
     * @return boolean
     */
    public static function compare(Rule $rule1, Rule $rule2)
    {
        return (array)$rule1->getMethod() == (array)$rule2->getMethod()
            && $rule1->getPattern() == $rule2->getPattern()
            && $rule1->getRoute() == $rule2->getRoute();
    }

    /**
     * Merges methods of two rules.
     *
     * If rules are equal returns clone of first rule. Otherwise returns new rule with merged methods.
     *
     * @param Rule $rule1
     * @param Rule $rule2
     * @return Rule
     */
    public static function merge(Rule $rule1, Rule $rule2)
    {
        if (self::compare($rule1, $rule2)) {
            return clone $rule1;
        } else {
            $result = clone $rule1;
            $result->setMethod(array_merge((array)$rule1->getMethod(), (array)$rule2->getMethod()));

            return $result;
        }
    }
}