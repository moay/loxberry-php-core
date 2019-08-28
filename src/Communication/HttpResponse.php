<?php

namespace LoxBerry\Communication;

/**
 * Class HttpResponse.
 */
class HttpResponse
{
    /** @var string|null */
    private $value;

    /** @var int */
    private $responseCode;

    /** @var string|null */
    private $rawResponse;

    /**
     * HttpResponse constructor.
     *
     * @param string|null $value
     * @param int         $responseCode
     * @param string|null $rawResponse
     */
    public function __construct(?string $value = null, int $responseCode = 500, ?string $rawResponse = null)
    {
        $this->value = $value;
        $this->responseCode = $responseCode;
        $this->rawResponse = $rawResponse;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
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
