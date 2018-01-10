<?php
/**
 * @link http://ipaya.cn/
 * @copyright Copyright (c) 2016 ipaya.cn
 */

namespace iPaya\Swoole\Psr7;

use Swoole\Http\Request as SwooleRequest;

class Request extends SwooleRequest
{
    private $swRequest;

    public function __construct(SwooleRequest $swRequest)
    {
        $this->swRequest = $swRequest;
    }

    /**
     * Get request's scheme
     *
     * @return string
     */
    public function getScheme()
    {
        return 'http';
    }

    /**
     * Get request's host
     *
     * @return string
     */
    public function getHost()
    {
        $hostInfo = explode(':', $this->swRequest->header['host']);
        return $hostInfo[0];
    }

    /**
     * Get request's port
     *
     * @return int|string
     */
    public function getPort()
    {
        if (isset($this->swRequest->server['server_port'])) {
            return $this->swRequest->server['server_port'];
        }
        return 'https' === $this->getScheme() ? 443 : 80;
    }

    /**
     * Return requested HTTP host
     *
     * @return string
     */
    public function getHttpHost()
    {
        $scheme = $this->getScheme();
        $host = $this->getHost();
        $port = $this->getPort();
        if (($scheme == 'http' && $port == 80) || ($scheme == 'https' && $port == 443)) {
            return $host;
        }
        return $host . ':' . $port;
    }

    /**
     * Get scheme and HTTP host
     *
     * @return string
     */
    public function getSchemeAndHost()
    {
        return $this->getScheme() . '://' . $this->getHttpHost();
    }

    /**
     * @param bool $asResource
     * @return bool|resource|string
     */
    public function getContent($asResource = false)
    {
        if ($asResource) {
            return fopen('php://input', 'rb');
        } else {
            return $this->swRequest->rawContent();
        }
    }

    /**
     * @return array|null
     */
    public function getServer()
    {
        return $this->swRequest->server ?? [];
    }

    public function getFiles()
    {
        return $this->swRequest->files ?? [];
    }

    public function getHeaders()
    {
        return $this->swRequest->header ?? [];
    }

    public function getCookies()
    {
        return $this->swRequest->cookie ?? [];
    }

    public function getQueryParams()
    {
        return $this->swRequest->get ?? [];
    }
}
