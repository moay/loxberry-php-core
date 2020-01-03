<?php

namespace LoxBerry\System\Localization;

/**
 * Class LanguageFileParser.
 */
class LanguageFileParser
{
    /** @var string */
    private $fileName;

    /** @var array */
    private $parsedTranslations = [];

    /**
     * LanguageFileParser constructor.
     *
     * @param string $fileName
     */
    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
        $this->parseTranslations();
    }

    /**
     * @param string      $section
     * @param string|null $key
     *
     * @return string|null
     */
    public function getTranslated(string $section, ?string $key = null): ?string
    {
        if (null === $key) {
            $key = explode('.', $section)[1] ?? '';
            $section = explode('.', $section)[0] ?? '';
        }

        if (array_key_exists($section, $this->parsedTranslations)
            && array_key_exists($key, $this->parsedTranslations[$section])) {
            return $this->parsedTranslations[$section][$key];
        }

        return '';
    }

    private function parseTranslations()
    {
        if (!file_exists($this->fileName) || !is_readable($this->fileName)) {
            throw new \InvalidArgumentException(sprintf('Non existing translation file %s requested.', $this->fileName));
        }

        $contents = file($this->fileName, FILE_SKIP_EMPTY_LINES);
        if (!$contents) {
            throw new \InvalidArgumentException(sprintf('Malformed translation file %s requested. Unable to parse.', $this->fileName));
        }

        $filteredContents = $this->removeCommentLines($contents);
        $section = '';
        foreach ($filteredContents as $line) {
            if (preg_match('/\[(\S+)\]/', $line, $matches)) {
                $section = $matches[1];
                continue;
            }
            if (preg_match('/(\S+)="(.*)"/', $line, $matches)) {
                $this->parsedTranslations[$section][$matches[1]] = trim($matches[2]);
                continue;
            }
            if (preg_match('/(\S+)=(.*)/', $line, $matches)) {
                $this->parsedTranslations[$section][$matches[1]] = trim($matches[2]);
            }
        }
    }

    /**
     * @param array $contents
     *
     * @return array
     */
    private function removeCommentLines(array $contents): array
    {
        return array_filter($contents, function ($line) {
            return !in_array(substr($line, 0, 1), ['#', '/', ';']);
        });
    }
}
