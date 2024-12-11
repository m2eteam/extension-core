<?php

declare(strict_types=1);

namespace M2E\Core\Helper;

class Data
{
    public const STATUS_ERROR = 1;
    public const STATUS_WARNING = 2;
    public const STATUS_SUCCESS = 3;

    public const INITIATOR_UNKNOWN = 0;
    public const INITIATOR_USER = 1;
    public const INITIATOR_EXTENSION = 2;
    public const INITIATOR_DEVELOPER = 3;

    public const INITIATORS = [
        self::INITIATOR_UNKNOWN,
        self::INITIATOR_USER,
        self::INITIATOR_EXTENSION,
        self::INITIATOR_DEVELOPER,
    ];

    // ----------------------------------------

    public static function validateInitiator(int $initiator): void
    {
        if (!in_array($initiator, self::INITIATORS, true)) {
            throw new \M2E\Core\Model\Exception\Logic("Initiator '$initiator' not valid.");
        }
    }

    // ----------------------------------------

    public static function escapeJs($string)
    {
        if ($string === null) {
            return '';
        }

        return str_replace(
            ["\\", "\n", "\r", "\"", "'"],
            ["\\\\", "\\n", "\\r", "\\\"", "\\'"],
            $string
        );
    }

    public static function escapeHtml($data, $allowedTags = null, $flags = ENT_COMPAT)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $item) {
                $result[] = self::escapeHtml($item, $allowedTags, $flags);
            }
        } else {
            $data = (string)$data;
            // process single item
            if ($data !== '') {
                if (is_array($allowedTags) && !empty($allowedTags)) {
                    $allowed = implode('|', $allowedTags);

                    $pattern = '/<([\/\s\r\n]*)(' . $allowed . ')' .
                        '((\s+\w+=["\'][\w\s\%\?=\&#\/\.,;:_\-\(\)]*["\'])*[\/\s\r\n]*)>/si';
                    $result = preg_replace($pattern, '##$1$2$3##', $data);

                    $result = htmlspecialchars($result, $flags);

                    $pattern = '/##([\/\s\r\n]*)(' . $allowed . ')' .
                        '((\s+\w+=["\'][\w\s\%\?=\&#\/\.,;:_\-\(\)]*["\'])*[\/\s\r\n]*)##/si';
                    $result = preg_replace($pattern, '<$1$2$3>', $result);
                } else {
                    $result = htmlspecialchars($data, $flags);
                }
            } else {
                $result = $data;
            }
        }

        return $result;
    }

    // ----------------------------------------

    public static function deEscapeHtml($data, $flags = ENT_COMPAT)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $item) {
                $result[] = self::deEscapeHtml($item, $flags);
            }
        } else {
            // process single item
            if ($data !== '') {
                $result = htmlspecialchars_decode($data, $flags);
            } else {
                $result = $data;
            }
        }

        return $result;
    }

    // ----------------------------------------

    public static function convertStringToSku($title)
    {
        $skuVal = strtolower($title);
        $skuVal = str_replace([
            " ",
            ":",
            ",",
            ".",
            "?",
            "*",
            "+",
            "(",
            ")",
            "&",
            "%",
            "$",
            "#",
            "@",
            "!",
            '"',
            "'",
            ";",
            "\\",
            "|",
            "/",
            "<",
            ">",
        ], "-", $skuVal);

        return $skuVal;
    }

    public static function stripInvisibleTags($text)
    {
        $text = preg_replace(
            [
                // Remove invisible content
                '/<head[^>]*?>.*?<\/head>/siu',
                '/<style[^>]*?>.*?<\/style>/siu',
                '/<script[^>]*?.*?<\/script>/siu',
                '/<object[^>]*?.*?<\/object>/siu',
                '/<embed[^>]*?.*?<\/embed>/siu',
                '/<applet[^>]*?.*?<\/applet>/siu',
                '/<noframes[^>]*?.*?<\/noframes>/siu',
                '/<noscript[^>]*?.*?<\/noscript>/siu',
                '/<noembed[^>]*?.*?<\/noembed>/siu',

                // Add line breaks before & after blocks
                '/<((br)|(hr))/iu',
                '/<\/?((address)|(blockquote)|(center)|(del))/iu',
                '/<\/?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))/iu',
                '/<\/?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))/iu',
                '/<\/?((table)|(th)|(td)|(caption))/iu',
                '/<\/?((form)|(button)|(fieldset)|(legend)|(input))/iu',
                '/<\/?((label)|(select)|(optgroup)|(option)|(textarea))/iu',
                '/<\/?((frameset)|(frame)|(iframe))/iu',
            ],
            [
                ' ',
                ' ',
                ' ',
                ' ',
                ' ',
                ' ',
                ' ',
                ' ',
                ' ',
                "\n\$0",
                "\n\$0",
                "\n\$0",
                "\n\$0",
                "\n\$0",
                "\n\$0",
                "\n\$0",
                "\n\$0",
            ],
            $text
        );

        return $text;
    }

    public static function normalizeToUtf($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::normalizeToUtf($value);
            }
        } elseif (is_string($data)) {
            return \mb_convert_encoding($data, 'utf8');
        }

        return $data;
    }

    /**
     * @param string $string
     * @param int $neededLength
     * @param int $longWord
     * @param int $minWordLen
     * @param int $atEndOfWord
     *
     * @return string
     */
    public static function reduceWordsInString(
        $string,
        $neededLength,
        $longWord = 6,
        $minWordLen = 2,
        $atEndOfWord = '.'
    ) {
        $oldEncoding = \mb_internal_encoding();
        \mb_internal_encoding('UTF-8');

        $string = (string)$string;
        if (\mb_strlen($string) <= $neededLength) {
            \mb_internal_encoding($oldEncoding);

            return $string;
        }

        $longWords = [];
        foreach (explode(' ', $string) as $word) {
            if (\mb_strlen($word) >= $longWord && !preg_match('/\d/', $word)) {
                $longWords[$word] = \mb_strlen($word) - $minWordLen;
            }
        }

        $canBeReduced = 0;
        foreach ($longWords as $canBeReducedForWord) {
            $canBeReduced += $canBeReducedForWord;
        }

        $needToBeReduced = \mb_strlen($string) - $neededLength + (\count($longWords) * \mb_strlen($atEndOfWord));

        if ($canBeReduced < $needToBeReduced) {
            \mb_internal_encoding($oldEncoding);

            return $string;
        }

        $weightOfOneLetter = $needToBeReduced / $canBeReduced;
        foreach ($longWords as $word => $canBeReducedForWord) {
            $willReduced = ceil($weightOfOneLetter * $canBeReducedForWord);
            $reducedWord = \mb_substr($word, 0, mb_strlen($word) - (int)$willReduced) . $atEndOfWord;

            $string = str_replace($word, $reducedWord, $string);

            if (strlen($string) <= $neededLength) {
                break;
            }
        }

        \mb_internal_encoding($oldEncoding);

        return $string;
    }

    public static function arrayReplaceRecursive($base, $replacements)
    {
        $args = func_get_args();
        foreach (array_slice($args, 1) as $replacements) {
            $bref_stack = [&$base];
            $head_stack = [$replacements];

            do {
                end($bref_stack);

                $bref = &$bref_stack[key($bref_stack)];
                $head = array_pop($head_stack);

                unset($bref_stack[key($bref_stack)]);

                foreach (array_keys($head) as $key) {
                    if (isset($key, $bref, $bref[$key]) && is_array($bref[$key]) && is_array($head[$key])) {
                        $bref_stack[] = &$bref[$key];
                        $head_stack[] = $head[$key];
                    } else {
                        $bref[$key] = $head[$key];
                    }
                }
            } while (\count($head_stack));
        }

        return $base;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public static function toLowerCaseRecursive(array $data = []): array
    {
        if (empty($data)) {
            return $data;
        }

        $lowerCasedData = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = self::toLowerCaseRecursive($value);
            } else {
                $value = trim(strtolower($value));
            }
            $lowerCasedData[trim(strtolower($key))] = $value;
        }

        return $lowerCasedData;
    }

    // ----------------------------------------

    /**
     * @param $string
     * @param null $prefix
     * @param string $hashFunction (md5, sh1)
     *
     * @return string
     */
    public static function hashString($string, $hashFunction, $prefix = null): string
    {
        if (!is_callable($hashFunction)) {
            throw new \M2E\Core\Model\Exception\Logic('Hash function can not be called');
        }

        $hash = call_user_func($hashFunction, $string);

        return !empty($prefix) ? $prefix . $hash : $hash;
    }

    public static function md5String(string $string): string
    {
        return self::hashString($string, 'md5');
    }

    // ----------------------------------------

    /**
     * @param string|null $strParam
     * @param int|null $maxLength
     *
     * @return string
     */
    public static function generateUniqueHash($strParam = null, $maxLength = null): string
    {
        $hash = sha1(rand(1, 1000000) . microtime(true) . (string)$strParam);
        (int)$maxLength > 0 && $hash = substr($hash, 0, (int)$maxLength);

        return $hash;
    }

    /**
     * @param int[] $statuses
     *
     * @return int
     */
    public static function getMainStatus(array $statuses): int
    {
        foreach ([self::STATUS_ERROR, self::STATUS_WARNING] as $status) {
            if (in_array($status, $statuses)) {
                return $status;
            }
        }

        return self::STATUS_SUCCESS;
    }
}
