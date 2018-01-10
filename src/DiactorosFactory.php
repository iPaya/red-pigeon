<?php
/**
 * @link http://ipaya.cn/
 * @copyright Copyright (c) 2016 ipaya.cn
 */

namespace iPaya\Swoole\Psr7;

use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Stream;

class DiactorosFactory
{
    /**
     * @param Request $req
     * @return ServerRequest
     */
    public function createRequest(Request $req)
    {
        $body = new Stream('php://temp', 'wb+');
        $body->write($req->getContent(false));

        $request = new ServerRequest(
            $req->getServer(),
            $req->getFiles(),
            $req->getSchemeAndHost(),
            $req->server['request_method'] ?? null,
            $body,
            $req->getHeaders(),
            $req->getCookies(),
            $req->getQueryParams()
        );
        return $request;
    }
}
