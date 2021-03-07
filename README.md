# CorsMiddleware plugin for CakePHP

## Installation

You can install this plugin into your CakePHP application using [composer](https://getcomposer.org).

The recommended way to install composer packages is:

```
composer require kaz29/cakephp-cors-middleware-plugin
```

## Minimum setup

- Add the following configuration into config/app_local.php.

```
    'App' => [
        'cors' => [
            'allowUrls' => [
                'https://example.com', // your web site urls
                'https://app.example.com',
            ],
        ],
    ],
```

OR 

When injecting with environment variables, it looks like this...
```
    'App' => [
        'cors' => [
            'allowUrls' => explode(',', env('CORS_ALLOW_URL', ''))
        ],
    ],
```

- Add load middleware setting into Application::middleware method.

```
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        $middlewareQueue
            ->add(new CorsMiddleware(Configure::read('App.cors')))  // Add this line
            // Catch any exceptions in the lower layers,
            // and make an error page/response
            ->add(new ErrorHandlerMiddleware(Configure::read('Error')))
```
## Customize settings

```
    'App' => [
        'cors' => [
            'allowUrls' => [
                'https://example.com', // your web site urls
                'https://app.example.com',
            ],
            'allowMethods' => [
                'GET',
                'POST',
                'HEAD',
                'OPTIONS',
                'PUT',
                'DELETE',
            ],
            'allowHeaders' => [
                'Accept-Language',
                'content-type',
                'Accept',
                'Origin',
                'Cookie',
                'Content-Length',
                'Authorization',
            ],
            'exposeHeaders' => [],
        ],
    ],
```


## Author

Kazuhiro Watanabe - cyo [at] mac.com - [https://twitter.com/kaz_29](https://twitter.com/kaz_29)

## License

CorsMiddleware plugin for CakePHP is licensed under the MIT License - see the LICENSE file for details
