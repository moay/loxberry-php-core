<?php

namespace LoxBerry\Communication;

/**
 * Class HttpResponse.
 */
class HttpResponse
{
    /** @var string|null */
    private $content;

    /** @var int */
    private $responseCode;

    /** @var string|null */
    private $rawResponse;

    /**
     * HttpResponse constructor.
     *
     * @param string|null $content
     * @param int         $responseCode
     * @param string|null $rawResponse
     */
    public function __construct(?string $content = null, int $responseCode = 500, ?string $rawResponse = null)
    {
        $this->content = $content;
        $this->responseCode = $responseCode;
        $this->rawResponse = $rawResponse;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getResponseCode(): int
    {
        return $this->responseCode;
    }

    /**
     * @return string|null
     */
    public function getRawResponse(): ?string
    {
        return $this->rawResponse;
    }
}
