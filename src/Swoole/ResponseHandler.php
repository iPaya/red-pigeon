<?php
/**
 * @copyright 2018 Di Zhang <zhangdi_me@163.com>
 */

namespace RedPigeon\Swoole;


use Psr\Http\Message\ResponseInterface;
use Swoole\Http\Response;

class ResponseHandler
{
    public $maxBodySize = 2 * 1024 * 1024 - 1;
    private $swResponse;


    public function __construct(Response $swResponse)
    {
        $this->swResponse = $swResponse;
    }

    /**
     * @param ResponseInterface $resp
     */
    public function handle(ResponseInterface $resp)
    {
        $swResponse = $this->swResponse;
        $headers = $resp->getHeaders();
        foreach ($headers as $name => $values) {
            $swResponse->header($name, implode(", ", $values));
        }

        foreach ($resp->getHeader('Set-Cookie') as $cookie) {
            $cookieInfo = $this->createCookie($cookie);
            $swResponse->cookie($cookieInfo['name'], $cookieInfo['value'], $cookieInfo['expire'], $cookieInfo['path'], $cookieInfo['domain'], $cookieInfo['secure'], $cookieInfo['httpOnly']);
        }

        $swResponse->status($resp->getStatusCode());

        $body = $resp->getBody();
        $body->rewind();
        $swResponse->write($body->getContents());

        $swResponse->end();
    }

    protected function createCookie($cookie)
    {
        foreach (explode(';', $cookie) as $part) {
            $part = trim($part);
            $data = explode('=', $part, 2);
            $name = $data[0];
            $value = isset($data[1]) ? trim($data[1], " \n\r\t\0\x0B\"") : null;
            if (!isset($cookieName)) {
                $cookieName = $name;
                $cookieValue = $value;
                continue;
            }
            if ('expires' === strtolower($name) && null !== $value) {
                $cookieExpire = new \DateTime($value);
                continue;
            }
            if ('path' === strtolower($name) && null !== $value) {
                $cookiePath = $value;
                continue;
            }
            if ('domain' === strtolower($name) && null !== $value) {
                $cookieDomain = $value;
                continue;
            }
            if ('secure' === strtolower($name)) {
                $cookieSecure = true;
                continue;
            }
            if ('httponly' === strtolower($name)) {
                $cookieHttpOnly = true;
                continue;
            }
        }
        if (!isset($cookieName)) {
            throw new \InvalidArgumentException('The value of the Set-Cookie header is malformed.');
        }
        return [
            'name' => $cookieName,
            'value' => $cookieValue,
            'expire' => isset($cookieExpire) ? $cookieExpire : 0,
            'path' => isset($cookiePath) ? $cookiePath : '/',
            'domain' => isset($cookieDomain) ? $cookieDomain : null,
            'secure' => isset($cookieSecure),
            'httpOnly' => isset($cookieHttpOnly)
        ];
    }
}
