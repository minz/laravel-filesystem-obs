<?php


namespace Minz\Obs\Plugins;


use League\Flysystem\Plugin\AbstractPlugin;

class Bucket extends AbstractPlugin
{

    public function getMethod()
    {
        return 'bucket';
    }

    /**
     * @param $bucket
     * @return mixed
     */
    public function handle($bucket)
    {
        return $this->filesystem->getAdapter()->bucket($bucket);
    }
}