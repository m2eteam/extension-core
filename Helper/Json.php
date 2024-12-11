<?php

declare(strict_types=1);

namespace M2E\Core\Helper;

class Json
{
    /**
     * @param mixed $data
     * @param bool $throwError
     *
     * @return string|null
     * @throws \M2E\Core\Model\Exception\Logic
     */
    public static function encode($data, $throwError = true): ?string
    {
        if ($data === false) {
            return 'false';
        }

        $encoded = json_encode($data);
        if ($encoded !== false) {
            return $encoded;
        }

        $encoded = json_encode(\M2E\Core\Helper\Data::normalizeToUtf($data));
        if ($encoded !== false) {
            return $encoded;
        }

        if (!$throwError) {
            return null;
        }

        throw new \M2E\Core\Model\Exception\Logic(
            'Unable to encode to JSON.',
            ['error' => json_last_error_msg()]
        );
    }

    /**
     * @param mixed $json
     * @param bool $throwError
     *
     * @return array|null
     * @throws \M2E\Core\Model\Exception\Logic
     */
    public static function decode($json, bool $throwError = false): ?array
    {
        if ($json === null || $json === '' || strtolower($json) === 'null') {
            return null;
        }

        if (!is_string($json)) {
            return [];
        }

        $decoded = json_decode($json, true);
        if ($decoded !== null) {
            return $decoded;
        }

        if ($throwError) {
            throw new \M2E\Core\Model\Exception\Logic(
                'Unable to decode JSON.',
                ['source' => $json]
            );
        }

        return null;
    }
}
