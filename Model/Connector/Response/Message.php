<?php

namespace M2E\Core\Model\Connector\Response;

class Message extends \M2E\Core\Model\Response\Message
{
    public const SENDER_KEY = 'sender';
    public const CODE_KEY = 'code';

    public const SENDER_SYSTEM = 'system';
    public const SENDER_COMPONENT = 'component';

    private string $sender = self::SENDER_SYSTEM;
    /** @var string|int */
    private $code = null;

    public function initFromResponseData(array $responseData): void
    {
        parent::initFromResponseData($responseData);

        $this->sender = $responseData[self::SENDER_KEY];
        $this->code = $responseData[self::CODE_KEY];
    }

    public function initFromPreparedData(string $text, string $type, ?string $sender = null, ?string $code = null): void
    {
        parent::initFromPreparedData($text, $type);

        $this->sender = $sender ?? self::SENDER_SYSTEM;
        $this->code = $code ?? 0;
    }

    public function asArray(): array
    {
        return array_merge(parent::asArray(), [
            self::SENDER_KEY => $this->sender,
            self::CODE_KEY => $this->code,
        ]);
    }

    public function isSenderSystem(): bool
    {
        return $this->sender === self::SENDER_SYSTEM;
    }

    public function isSenderComponent(): bool
    {
        return $this->sender === self::SENDER_COMPONENT;
    }

    public function getCode()
    {
        return $this->code;
    }
}
