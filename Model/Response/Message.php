<?php

namespace M2E\Core\Model\Response;

class Message
{
    public const TEXT_KEY = 'text';
    public const TYPE_KEY = 'type';

    public const TYPE_ERROR = 'error';
    public const TYPE_WARNING = 'warning';
    public const TYPE_SUCCESS = 'success';
    public const TYPE_NOTICE = 'notice';

    private string $text = '';
    private string $type = self::TYPE_ERROR;

    public static function createError(string $text): self
    {
        $obj = new static();
        $obj->text = $text;
        $obj->type = self::TYPE_ERROR;

        return $obj;
    }

    public static function createWarning(string $text): self
    {
        $obj = new static();
        $obj->text = $text;
        $obj->type = self::TYPE_WARNING;

        return $obj;
    }

    public static function createNotice(string $text): self
    {
        $obj = new static();
        $obj->text = $text;
        $obj->type = self::TYPE_NOTICE;

        return $obj;
    }

    public static function createSuccess(string $text): self
    {
        $obj = new static();
        $obj->text = $text;
        $obj->type = self::TYPE_SUCCESS;

        return $obj;
    }

    public static function create(string $text, string $type): self
    {
        $obj = new static();
        $obj->text = $text;
        $obj->type = $type;

        return $obj;
    }

    // ----------------------------------------

    public function initFromResponseData(array $responseData): void
    {
        $this->text = $responseData[self::TEXT_KEY];
        $this->type = $responseData[self::TYPE_KEY];
    }

    public function initFromPreparedData(string $text, string $type): void
    {
        $this->text = $text;
        $this->type = $type;
    }

    public function initFromException(\Throwable $exception): void
    {
        $this->text = $exception->getMessage();
        $this->type = self::TYPE_ERROR;
    }

    // ----------------------------------------

    public function asArray(): array
    {
        return [
            self::TEXT_KEY => $this->text,
            self::TYPE_KEY => $this->type,
        ];
    }

    // ----------------------------------------

    public function getText(): string
    {
        return $this->text;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isError(): bool
    {
        return $this->type === self::TYPE_ERROR;
    }

    public function isWarning(): bool
    {
        return $this->type === self::TYPE_WARNING;
    }

    public function isSuccess(): bool
    {
        return $this->type === self::TYPE_SUCCESS;
    }

    public function isNotice(): bool
    {
        return $this->type === self::TYPE_NOTICE;
    }
}
