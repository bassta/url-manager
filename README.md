UrlManager
==========

A simple URL manager based on [nikic/fastroute](https://github.com/nikic/fastroute).

Install
-------

To install with composer add to composer.json:

```json
"require": {
    "bassta/url-manager": "^0.1"
},
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/bassta/url-manager"
    }
]
```

Usage
-----

Request parse example:

```php
<?php
require '/path/to/vendor/autoload.php';

$urlManager = new \UrlManager\UrlManager();

$urlManager->addRule(new \UrlManager\Rule('get', '/users', 'user/list'))
           ->addRule(new \UrlManager\Rule('POST', '/users', 'user/list'))
           ->addRule(new \UrlManager\Rule('GET', '/user/{id:\d+}', 'user/view'))
           ->addRule(new \UrlManager\Rule('GET', '/articles/{id:\d+}[/{title}]', 'article/view'));

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri        = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$routeInfo  = $urlManager->parseRequest($httpMethod, $uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
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
```