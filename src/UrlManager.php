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
            } else {
                $url .= $part;
            }
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