<?php


namespace Minz\Obs\Plugins;


use League\Flysystem\Plugin\AbstractPlugin;

class CreateSignedUrl extends AbstractPlugin
{

    public function getMethod()
    {
        return 'createSignedUrl';
    }

    /**
     * 获取签名URL
     *
     * @param string $key
     * @param int $expires
     * @param string $method
     * @return string
     */
    public function handle(string $key, int $expires = 3600, string $method = "GET")
    {
        return $this->filesystem->getAdapter()->createSignedUrl($key, $expires, $method);
    }
}