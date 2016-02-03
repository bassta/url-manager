<?php
/**
 * url_manager
 *
 * Copyright (c) 2016 Vasily Stashko
 */

class UrlManagerTest extends PHPUnit_Framework_TestCase
{
    public function testUrl()
    {
        $urlManager = new \UrlManager\UrlManager();
        $urlManager->addRule(new \UrlManager\Rule('GET', '/users', 'user/list'))
                   ->addRule(new \UrlManager\Rule('POST', '/users', 'user/list'))
                   ->addRule(new \UrlManager\Rule('GET', '/users[/]', 'user/list_op'))
                   ->addRule(new \UrlManager\Rule('POST', '/user/{id:\d+}', 'user/view'))
                   ->addRule(new \UrlManager\Rule('POST', '/user[/{id:\d+}]', 'user/view_op'));

        $this->assertEquals('/users', $urlManager->url('user/list'));
        $this->assertEquals('/users/', $urlManager->url('user/list_op'));
        $this->assertEquals('/user/1', $urlManager->url('user/view', [ 'id' => 1 ]));
        $this->assertEquals('/user/1?foo=bar', $urlManager->url('user/view', [ 'id' => 1, 'foo' => 'bar' ]));
        $this->assertEquals('/user/1?foo=bar&qwe=123', $urlManager->url('user/view', [ 'id' => 1, 'foo' => 'bar', 'qwe' => 123 ]));
        $this->assertEquals('/user/1', $urlManager->url('user/view_op', [ 'id' => 1 ]));
        $this->assertEquals('/user/1?foo=bar', $urlManager->url('user/view_op', [ 'id' => 1, 'foo' => 'bar' ]));
        $this->assertEquals('/user/1?foo=bar&qwe=123', $urlManager->url('user/view_op', [ 'id' => 1, 'foo' => 'bar', 'qwe' => 123 ]));
        $this->assertEquals('/user', $urlManager->url('user/view_op'));
    }

    public function testParseRequest()
    {
        $urlManager = new \UrlManager\UrlManager();

        $urlManager->addRule(new \UrlManager\Rule('GET', '/users', 'user/list'))
                   ->addRule(new \UrlManager\Rule('POST', '/users', 'user/list'))
                   ->addRule(new \UrlManager\Rule('GET', '/user/{id:\d+}', 'user/view'))
                   ->addRule(new \UrlManager\Rule('GET', '/man[/{id:\d+}]', 'man/view_op'));

        $routeInfo = $urlManager->parseRequest('GET', '/not_found');
        $this->assertEquals(FastRoute\Dispatcher::NOT_FOUND, $routeInfo[0]);

        $routeInfo = $urlManager->parseRequest('PUT', '/users');
        $this->assertEquals(FastRoute\Dispatcher::METHOD_NOT_ALLOWED, $routeInfo[0]);

        $routeInfo = $urlManager->parseRequest('GET', '/users');
        $this->assertEquals(FastRoute\Dispatcher::FOUND, $routeInfo[0]);
        $this->assertEquals('user/list', $routeInfo[1]);

        $routeInfo = $urlManager->parseRequest('GET', '/user/1');
        $this->assertEquals(FastRoute\Dispatcher::FOUND, $routeInfo[0]);
        $this->assertEquals('user/view', $routeInfo[1]);
        $this->assertEquals([ 'id' => 1 ], $routeInfo[2]);

        $routeInfo = $urlManager->parseRequest('GET', '/man/1');
        $this->assertEquals(FastRoute\Dispatcher::FOUND, $routeInfo[0]);
        $this->assertEquals('man/view_op', $routeInfo[1]);
        $this->assertEquals([ 'id' => 1 ], $routeInfo[2]);

        $routeInfo = $urlManager->parseRequest('GET', '/man');
        $this->assertEquals(FastRoute\Dispatcher::FOUND, $routeInfo[0]);
        $this->assertEquals('man/view_op', $routeInfo[1]);
        $this->assertEquals([], $routeInfo[2]);
    }

    public function testSetDefault()
    {
        $urlManager = new \UrlManager\UrlManager();

        $urlManager->addRule((new \UrlManager\Rule('GET', '/user[/{action}]', 'user/action'))->setDefault([ 'action' => 'view' ]));

        $routeInfo = $urlManager->parseRequest('GET', '/user');
        $this->assertEquals(FastRoute\Dispatcher::FOUND, $routeInfo[0]);
        $this->assertEquals('user/action', $routeInfo[1]);
        $this->assertEquals([ 'action' => 'view' ], $routeInfo[2]);

        $routeInfo = $urlManager->parseRequest('GET', '/user/view');
        $this->assertEquals(FastRoute\Dispatcher::FOUND, $routeInfo[0]);
        $this->assertEquals('user/action', $routeInfo[1]);
        $this->assertEquals([ 'action' => 'view' ], $routeInfo[2]);

        $routeInfo = $urlManager->parseRequest('GET', '/user/print');
        $this->assertEquals(FastRoute\Dispatcher::FOUND, $routeInfo[0]);
        $this->assertEquals('user/action', $routeInfo[1]);
        $this->assertEquals([ 'action' => 'print' ], $routeInfo[2]);
    }
}