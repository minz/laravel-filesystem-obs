<?php


namespace Minz\Obs;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Minz\Obs\Plugins\Bucket;
use Minz\Obs\Plugins\CreateSignedUrl;
use Minz\Obs\Plugins\UploadToken;

class ObsServiceProvider extends ServiceProvider
{
    public function register()
    {

    }

    public function boot()
    {
        Storage::extend("obs", function ($app, $config) {
            $adapter = new ObsAdapter(
                $config['accessKey'],
                $config['secretKey'],
                $config['bucket'],
                $config['endpoint'],
                $config['isCName'],
                $config['sslVerify'],
                $config['buckets']
            );
            $fileSystem = new Filesystem($adapter);
            $fileSystem->addPlugin(new UploadToken());
            $fileSystem->addPlugin(new Bucket());
            $fileSystem->addPlugin(new CreateSignedUrl());
            return $fileSystem;
        });
    }
}