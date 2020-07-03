<?php


namespace Minz\Obs;



use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;
use Obs\ObsClient;
use Minz\Obs\Exception\BucketsConfigErrorException;

class ObsAdapter extends AbstractAdapter
{
    protected $ak;
    protected $sk;
    protected $endpoint;
    protected $bucket;
    protected $isCName;
    protected $useSsl;
    protected $buckets;

    protected $client;

    /**
     * ObsAdapter constructor.
     * @param string $ak
     * @param string $sk
     * @param string $bucket
     * @param string $endpoint
     * @param bool $isCName
     * @param bool $useSsl
     * @param array $buckets
     */
    public function __construct(string $ak, string $sk, string $bucket, string $endpoint, bool $isCName, bool $useSsl, array $buckets = [])
    {
        $this->ak = $ak;
        $this->sk = $sk;
        $this->bucket = $bucket;
        $this->endpoint = $endpoint;
        $this->isCName = $isCName;
        $this->useSsl = $useSsl;
        $this->buckets = $buckets;
        $this->initClient();
    }

    public function bucket(string $bucket) {
        if (!$bucketConfig = $this->buckets[$bucket]) {
            throw new BucketsConfigErrorException("buckets配置中未定义{$bucket}");
        }
        $this->bucket = $bucketConfig['bucket'];
        $this->endpoint = $bucketConfig['endpoint'];
        $this->isCName = $bucketConfig['isCName'];
        $this->useSsl = $bucketConfig['sslVerify'];
        unset($this->client);
        $this->initClient();
        return $this;
    }

    public function initClient()
    {
        if (empty($this->client)) {
            $this->client = new ObsClient([
                'key' => $this->ak,
                'secret' => $this->sk,
                'endpoint' => $this->endpoint,
            ]);
        }
        return $this;
    }

    public function uploadToken($key = null, int $expires = 3600, array $formParams = [])
    {
        $tokenModel = $this->client->createPostSignature([
            'Bucket' => $this->bucket,
            'Key' => $key,
            'Expires' => $expires,
            'FormParams' => $formParams
        ]);
        $originPolicy = $tokenModel->get("OriginPolicy");
        $originPolicyArray = json_decode($originPolicy, true);
        if (!$originPolicyArray) {
            $originPolicy = str_replace("},]}", "}]}", $originPolicy);
            $originPolicyArray = json_decode($originPolicy, true);
        }
        $tokenModel->set('OriginPolicy', $originPolicyArray);
        $tokenModel->add('host', $this->normalizeHost());
        return $tokenModel->toArray();
    }

    /**
     * 获取签名的URL
     *
     * @param string $key
     * @param int $expires
     * @param string $method
     * @return string url
     */
    public function createSignedUrl(string $key, int $expires = 3600, string $method = "GET")
    {
        $tokenModel = $this->client->createSignedUrl([
            'Method' => $method,
            'Bucket' => $this->bucket,
            'Key' => $key,
            'Expires' => $expires
        ]);
        return $tokenModel->get("SignedUrl");
    }

    public function write($path, $contents, Config $config)
    {
        // TODO: Implement write() method.
    }

    public function writeStream($path, $resource, Config $config)
    {
        // TODO: Implement writeStream() method.
    }

    public function update($path, $contents, Config $config)
    {
        // TODO: Implement update() method.
    }

    public function updateStream($path, $resource, Config $config)
    {
        // TODO: Implement updateStream() method.
    }

    public function rename($path, $newpath)
    {
        // TODO: Implement rename() method.
    }

    public function copy($path, $newpath)
    {
        // TODO: Implement copy() method.
    }

    public function delete($path)
    {
        // TODO: Implement delete() method.
    }

    public function deleteDir($dirname)
    {
        // TODO: Implement deleteDir() method.
    }

    public function createDir($dirname, Config $config)
    {
        // TODO: Implement createDir() method.
    }

    public function setVisibility($path, $visibility)
    {
        // TODO: Implement setVisibility() method.
    }

    public function has($path)
    {
        // TODO: Implement has() method.
    }

    public function read($path)
    {
        // TODO: Implement read() method.
    }

    public function readStream($path)
    {
        // TODO: Implement readStream() method.
    }

    public function listContents($directory = '', $recursive = false)
    {
        // TODO: Implement listContents() method.
    }

    public function getMetadata($path)
    {
        // TODO: Implement getMetadata() method.
    }

    public function getSize($path)
    {
        // TODO: Implement getSize() method.
    }

    public function getMimetype($path)
    {
        // TODO: Implement getMimetype() method.
    }

    public function getTimestamp($path)
    {
        // TODO: Implement getTimestamp() method.
    }

    public function getVisibility($path)
    {
        // TODO: Implement getVisibility() method.
    }

    protected function normalizeHost()
    {
        if ($this->isCName) {
            $domain = $this->endpoint;
        } else {
            $domain = "{$this->bucket}.{$this->endpoint}";
        }
        if ($this->useSsl) {
            $domain = "https://" . $domain;
        } else {
            $domain = "http://" . $domain;
        }
        return $domain;
    }
}