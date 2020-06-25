<?php


namespace Minz\Obs\Plugins;


use League\Flysystem\Plugin\AbstractPlugin;

class UploadToken extends AbstractPlugin
{

    public function getMethod()
    {
        return 'uploadToken';
    }

    /**
     * @param null $key
     * @param int $expire
     * @param array $params
     * @return mixed
     */
    public function handle($key = null, int $expire = 3600, array $params = [])
    {
        return $this->filesystem->getAdapter()->uploadToken($key, $expire);
    }
}