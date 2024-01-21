<?php

namespace Krzysztofzylka\Env;

use Exception;

class Env
{

    /**
     * Env filepath
     * @var string|array
     */
    private string|array $filePaths;

    /**
     * Constructs a new instance of the class.
     * @param string|array $paths The path(s) of the file(s) to be loaded.
     * @throws Exception If any of the files specified in $paths do not exist.
     */
    public function __construct(string|array $paths)
    {
        $this->filePaths = is_string($paths) ? [$paths] : $paths;

        foreach ($this->filePaths as $path) {
            if (!file_exists($path)) {
                throw new Exception('File not found', 404);
            }
        }
    }

    /**
     * Loads the contents of a file, processes the content, and sorts the environment variables.
     * @return bool Returns true if the file is successfully loaded and processed, false otherwise.
     */
    public function load(): bool
    {
        foreach ($this->filePaths as $filePath) {
            $fileContents = file_get_contents($filePath);

            if ($fileContents === false) {
                return false;
            }

            $lines = explode(PHP_EOL, $fileContents);

            foreach ($lines as $line) {
                $this->processContent($line);
            }

            ksort($_ENV);
        }

        return true;
    }

    /**
     * Process content of the given string
     * @param string $content The content to be processed
     * @return void
     */
    private function processContent(string $content): void
    {
        $content = ltrim($content);

        if (str_starts_with($content, '#') || empty($content)) {
            return;
        }

        $contentParts = explode('=', $content, 2);

        if (count($contentParts) < 2) {
            return;
        }

        $name = $contentParts[0];
        $value = $contentParts[1];
        $value = $this->parseValue($value);
        $_ENV[strtoupper($name)] = $value;
    }

    /**
     * Parse a given value and return the appropriate data type
     * @param mixed $value The value to be parsed
     * @return mixed The parsed value
     */
    private function parseValue(mixed $value): mixed
    {
        if (str_starts_with($value, '"') && str_ends_with($value, '"') || str_starts_with($value, "'") && str_ends_with($value, "'")) {
            return substr($value, 1, -1);
        } elseif (preg_match("/^\d+$/", $value)) {
            return (int)$value;
        } elseif (preg_match("/^\d+\.\d+$/", $value)) {
            return (float)$value;
        }

        return match (strtolower($value)) {
            'false' => false,
            'true' => true,
            'null' => null,
            default => $value,
        };
    }

}