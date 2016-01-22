<?php
/**
 * url_manager
 *
 * Copyright (c) 2016 Vasily Stashko
 */

class RuleTest extends PHPUnit_Framework_TestCase
{
    public function testCompare()
    {
        $this->assertTrue(\UrlManager\Rule::compare(
            new \UrlManager\Rule('GET', '/', '/'), new \UrlManager\Rule('GET', '/', '/')
        ));
        $this->assertTrue(\UrlManager\Rule::compare(
            new \UrlManager\Rule('GET', '/', '/'), new \UrlManager\Rule(['GET'], '/', '/')
        ));
        $this->assertFalse(\UrlManager\Rule::compare(
            new \UrlManager\Rule('GET', '/', '/'), new \UrlManager\Rule('get', '/', '/')
        ));
        $this->assertFalse(\UrlManager\Rule::compare(
            new \UrlManager\Rule('GET', '/', '/'), new \UrlManager\Rule('POST', '/', '/')
        ));
        $this->assertFalse(\UrlManager\Rule::compare(
            new \UrlManager\Rule('GET', '/', '/'), new \UrlManager\Rule('GET', '/q', '/')
        ));
        $this->assertFalse(\UrlManager\Rule::compare(
            new \UrlManager\Rule('GET', '/', '/'), new \UrlManager\Rule('GET', '/', '/q')
        ));
    }

    public function testMerge()
    {
        $this->assertEquals(
            new \UrlManager\Rule('GET', '/', '/'),
            \UrlManager\Rule::merge(
                new \UrlManager\Rule('GET', '/', '/'),
                new \UrlManager\Rule('GET', '/', '/')
            )
        );
        $this->assertEquals(
            new \UrlManager\Rule('GET', '/', '/'),
            \UrlManager\Rule::merge(
                new \UrlManager\Rule('GET', '/', '/'),
                new \UrlManager\Rule(['GET'], '/', '/')
            )
        );
        $this->assertEquals(
            new \UrlManager\Rule(['GET', 'POST'], '/', '/'),
            \UrlManager\Rule::merge(
                new \UrlManager\Rule('GET', '/', '/'),
                new \UrlManager\Rule('POST', '/', '/')
            )
        );
        $this->assertEquals(
            new \UrlManager\Rule(['GET', 'POST'], '/', '/'),
            \UrlManager\Rule::merge(
                new \UrlManager\Rule('GET', '/', '/'),
                new \UrlManager\Rule(['POST'], '/', '/')
            )
        );
        $this->assertEquals(
            new \UrlManager\Rule(['GET', 'POST'], '/', '/'),
            \UrlManager\Rule::merge(
                new \UrlManager\Rule(['GET'], '/', '/'),
                new \UrlManager\Rule('POST', '/', '/')
            )
        );
        $this->assertEquals(
            new \UrlManager\Rule(['GET', 'POST'], '/', '/'),
            \UrlManager\Rule::merge(
                new \UrlManager\Rule(['GET'], '/', '/'),
                new \UrlManager\Rule(['POST'], '/', '/')
            )
        );
    }
}
