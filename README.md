UrlManager
==========

A simple URL manager based on [nikic/fastroute](https://github.com/nikic/fastroute).

Install
-------

To install with composer:

```sh
composer require bassta/url-manager
```

Usage
-----

Request parse example:

```php
<?php
require '/path/to/vendor/autoload.php';

$urlManager = new \UrlManager\UrlManager();

$urlManager->addRule(new \UrlManager\Rule('GET', '/users', 'user/list'))
           ->addRule(new \UrlManager\Rule('POST', '/users', 'user/list'))
           ->addRule(new \UrlManager\Rule('GET', '/user/{id:\d+}', 'user/view'))
           ->addRule(new \UrlManager\Rule('GET', '/articles/{id:\d+}[/{title}]', 'article/view'))
           ->addRule((new \UrlManager\Rule('GET', '/user[/{action}]', 'user/action'))->setDefault([ 'action' => 'view' ]));

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri        = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$routeInfo  = $urlManager->parseRequest($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        // ... call $handler with $vars
        break;
}
```

Rule::setDefault() method used to set optional suffix default value.

```php
new \UrlManager\Rule('GET', '/user/{action}', 'user/action')
// will match '/user/view' or '/user/print' but not '/user'

(new \UrlManager\Rule('GET', '/user[/{action}]', 'user/action'))->setDefault([ 'action' => 'view' ])
// will match '/user' with $routeInfo[2]
//  array(1) {
//    'action' =>
//    string(4) "view"
//  }
```

URL create example:

```php
<?php
require '/path/to/vendor/autoload.php';

$urlManager = new \UrlManager\UrlManager();
$urlManager->addRule(new \UrlManager\Rule('GET', '/users', 'user/list'))
           ->addRule(new \UrlManager\Rule('POST', '/users', 'user/list'))
           ->addRule(new \UrlManager\Rule('GET', '/user/{id:\d+}', 'user/view'));

$urlManager->url('user/list');
// returns '/users'

$urlManager->url('user/view', [ 'id' => 1 ]);
// returns '/user/1'

$urlManager->url('user/view', [ 'id' => 1, 'foo' => 'bar' ]);
// returns '/user/1?foo=bar'
```