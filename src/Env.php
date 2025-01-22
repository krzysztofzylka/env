<?php

namespace Krzysztofzylka\Env;

use Exception;

class Env
{

    /**
     * Loads environment variables from the system into the $_ENV array.
     *
     * This method iterates through all available environment variables
     * retrieved from the getenv() function and assigns them to the $_ENV array.
     * This is useful when we want to access environment variables globally
     * in PHP scripts.
     *
     * @return bool Returns true if the operation was successful.
     */
    public function loadFromSystem(): bool
    {
        $_ENV = array_merge($_ENV, getenv());

        return true;
    }

    /**
     * Loads the contents of a file, processes the content, and sorts the environment variables.
     * @return bool Returns true if the file is successfully loaded and processed, false otherwise.
     * @throws Exception
     */
    public function loadFromFile(string|array $paths): bool
    {
        if (is_string($paths)) {
            $paths = [$paths];
        }

        foreach ($paths as $filePath) {
            if (!file_exists($filePath)) {
                throw new Exception('File not found', 404);
            }

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
        $value = trim($value, ' \0');

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