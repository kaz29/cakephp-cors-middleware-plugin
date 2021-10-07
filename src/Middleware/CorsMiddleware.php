<?php
declare(strict_types=1);

namespace CorsMiddleware\Middleware;

use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CorsMiddleware implements MiddlewareInterface
{
    protected $config = [
        'statusCode' => 200,
        'allowUrls' => [],
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
        'maxAge' => null,
        'exposeHeaders' => [],
    ];

    /**
     * Constructor
     *
     * @param array $config The options to use.
     * @see self::$config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config + $this->config;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $allow_url = $this->checkAllowURL($request);
        if ($allow_url !== false) {
            if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
                return $this->allowCors(new EmptyResponse($this->config['statusCode']), $allow_url);
            }
        }

        $response = $handler->handle($request);

        if ($allow_url !== false) {
            $response = $this->allowCors($response, $allow_url);
        }

        return $response;
    }

    protected function checkAllowURL(ServerRequestInterface $request)
    {
        $origin = $request->getHeader('Origin');
        if (!$origin) {
            return false;
        }

        foreach ($origin as $url) {
            foreach ($this->config['allowUrls'] as $allow_url) {
                if ($url === $allow_url) {
                    return $allow_url;
                }
            }
        }

        return false;
    }

    protected function allowCors(ResponseInterface $response, $allow_url): ResponseInterface
    {
        $response = $response
            ->withHeader('Access-Control-Allow-Origin', $allow_url)
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Access-Control-Allow-Methods', implode(',', $this->config['allowMethods']))
            ->withHeader('Access-Control-Allow-Headers', implode(',', $this->config['allowHeaders']));

        if (is_array($this->config['exposeHeaders'])) {
            $response = $response
                ->withHeader('Access-Control-Expose-Headers', implode(',', $this->config['exposeHeaders']));
        }

        if ($this->config['maxAge'] !== null) {
            $response = $response
                ->withHeader('Access-Control-Max-Age', $this->config['maxAge']);
        }

        return $response;
    }
}
