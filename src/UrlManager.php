<?php
/**
 * url_manager
 *
 * Copyright (c) 2016 Vasily Stashko
 */

namespace UrlManager;

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\Dispatcher\GroupCountBased as Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser;
use FastRoute\RouteParser\Std;

class UrlManager
{
    /** @var RouteCollector $routeCollector */
    protected $routeCollector;
    /** @var RouteParser $parser */
    protected $parser;
    /** @var Rule[] $rules */
    protected $rules;

    /**
     * Adds a rule to UrlManager.
     *
     * @param Rule $rule
     * @return $this
     */
    public function addRule(Rule $rule)
    {
        if (isset($this->rules[$rule->getRoute()])) {
            $this->rules[$rule->getRoute()] = Rule::merge($this->rules[$rule->getRoute()], $rule);
        } else {
            $this->rules[$rule->getRoute()] = $rule;
        }

        return $this;
    }

    /**
     * Creates a URL using the given route and query parameters.
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function url($route, $params = [])
    {
        $variants = $this->getParser()->parse($this->rules[$route]->getPattern());
        $variant  = end($variants);
        $option   = end($variant);

        if (is_array($option)) {
            if (!isset($params[$option[0]])) {
                $variant = reset($variants);
            }
        }

        $url = '';
        foreach ($variant as $part) {
            if (is_array($part)) {
                $url .= $params[$part[0]];
                unset($params[$part[0]]);
            } else {
                $url .= $part;
            }
        }

        if (!empty($params)) {
            $pairs = [];

            foreach ($params as $key => $value) {
                $pairs[] = $key.'='.$value;
            }

            $url .= '?'.implode('&', $pairs);
        }

        return $url;
    }

    /**
     * @return RouteParser
     */
    protected function getParser()
    {
        if (!$this->parser) {
            $this->parser = new Std();
        }

        return $this->parser;
    }

    /**
     * Parses the user request against the provided HTTP method verb and URI.
     *
     * Returns array (from FastRoute\Dispatcher) with one of the following formats:
     *
     *     [FastRoute\Dispatcher::NOT_FOUND]
     *     [FastRoute\Dispatcher::METHOD_NOT_ALLOWED, ['GET', 'OTHER_ALLOWED_METHODS']]
     *     [FastRoute\Dispatcher::FOUND, $handler, ['varName' => 'value', ...]]
     *
     * @param string $method
     * @param string $uri
     * @return array
     */
    public function parseRequest($method, $uri)
    {
        $this->buildRules();

        $data       = $this->routeCollector->getData();
        $dispatcher = new Dispatcher($data);

        $routeInfo = $dispatcher->dispatch($method, $uri);

        if ($routeInfo[0] == Dispatcher::FOUND) {
            $routeInfo[2] = array_merge($this->rules[$routeInfo[1]]->getDefault(), $routeInfo[2]);
        }

        return $routeInfo;
    }

    protected function buildRules()
    {
        $this->routeCollector = new RouteCollector($this->getParser(), new GroupCountBased());

        foreach ($this->rules as $rule) {
            $this->routeCollector->addRoute($rule->getMethod(), $rule->getPattern(), $rule->getRoute());
        }
    }
}