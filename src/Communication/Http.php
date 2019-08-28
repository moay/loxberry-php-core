<?php

namespace LoxBerry\Communication;

use LoxBerry\Communication\ValueCache\HttpValueCache;
use LoxBerry\ConfigurationParser\MiniserverInformation;
use LoxBerry\System\LowLevelExecutor;

/**
 * Class Http.
 */
class Http extends AbstractCachingCommunicator
{
    /** @var LowLevelExecutor */
    private $lowLevel;

    /**
     * Http constructor.
     *
     * @param HttpValueCache   $cache
     * @param LowLevelExecutor $lowLevelExecutor
     */
    public function __construct(HttpValueCache $cache, LowLevelExecutor $lowLevel)
    {
        $this->cache = $cache;
        $this->lowLevel = $lowLevel;
    }

    /**
     * @param MiniserverInformation $miniserver
     * @param string                $block
     * @param $value
     * @param bool $ifChanged
     *
     * @return bool
     */
    public function setBlockValue(MiniserverInformation $miniserver, string $block, $value, bool $ifChanged = true): bool
    {
        if (
            $ifChanged
            && !$this->fullPushRequired($miniserver)
            && !$this->miniserverRebooted($miniserver)
            && $this->valueMatchesCache($miniserver, $block, $value)
        ) {
            return true;
        }

        $response = $this->call($miniserver, sprintf(
            '/dev/sps/io/%s/%s',
            $block,
            $value
        ));

        $this->cache->put($block, $value, $miniserver->getIpAddress(), $miniserver->getPort());

        return 200 === $response->getResponseCode() && (string) $value == $response->getValue();
    }

    /**
     * @param MiniserverInformation $miniserver
     * @param array                 $data
     * @param bool                  $ifChanged
     *
     * @return array
     */
    public function setBlockValues(MiniserverInformation $miniserver, array $data, bool $ifChanged = true): array
    {
        if ($ifChanged) {
            $data = $this->handleCaching($miniserver, $data);
        }

        $responseArray = [];
        foreach ($data as $key => $value) {
            $responseArray[$key] = $this->setBlockValue($miniserver, $key, $value, false);
        }

        return $responseArray;
    }

    /**
     * @param MiniserverInformation $miniserver
     * @param string                $block
     *
     * @return mixed
     */
    public function getBlockValue(MiniserverInformation $miniserver, string $block)
    {
        $response = $this->call($miniserver, sprintf(
            '/dev/sps/io/%s',
            $block
        ));

        if (200 !== $response->getResponseCode()) {
            return null;
        }

        if (is_numeric($response->getValue())) {
            return $response->getValue() + 0;
        }

        return $response->getValue();
    }

    /**
     * @param MiniserverInformation $miniserver
     * @param string                $command
     *
     * @return HttpResponse
     */
    public function call(MiniserverInformation $miniserver, string $command): HttpResponse
    {
        $baseUrl = sprintf(
            'http://%s@%s:%s',
            $miniserver->getCredentials(),
            $miniserver->getIpAddress(),
            $miniserver->getPort() ?? 80
        );

        $response = $this->lowLevel->fileGetContents($baseUrl.$command);
        if (!$response) {
            throw new \RuntimeException('Webservices http call to miniserver failed');
        }

        return $this->parseRawXmlResponse($response);
    }

    /**
     * @param string $response
     *
     * @return HttpResponse
     */
    private function parseRawXmlResponse(string $response): HttpResponse
    {
        preg_match('/value\=\"(.*?)\"/', $response, $matches);
        $value = $matches[1];

        preg_match('/Code\=\"(.*?)\"/', $response, $matches);
        $code = $matches[1];

        return new HttpResponse($value, $code, $response);
    }
}
