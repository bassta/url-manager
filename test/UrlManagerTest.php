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
        $this->assertEquals('/user/1', $urlManager->url('user/view_op', [ 'id' => 1 ]));
        $this->assertEquals('/user', $urlManager->url('user/view_op'));
    }

    public function testParseRequest()
    {

    }
}