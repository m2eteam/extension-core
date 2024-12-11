<?php

declare(strict_types=1);

namespace M2E\Core\Model\Connector\Client;

use M2E\Core\Model\Connector\RequestBuilder;
use M2E\Core\Model\Connector\ResponseParser;

class Single
{
    private \M2E\Core\Model\Connector\ProtocolInterface $protocol;
    private ConfigInterface $config;
    private RequestBuilder $requestBuilder;
    private Curl $curl;
    /** @var \M2E\Core\Model\Connector\Client\ModuleInfoInterface */
    private ModuleInfoInterface $moduleInfo;

    public function __construct(
        \M2E\Core\Model\Connector\ProtocolInterface $protocol,
        ConfigInterface $config,
        \M2E\Core\Model\Connector\Client\ModuleInfoInterface $moduleInfo,
        RequestBuilder $requestBuilder,
        Curl $curl
    ) {
        $this->protocol = $protocol;
        $this->config = $config;
        $this->requestBuilder = $requestBuilder;
        $this->curl = $curl;
        $this->moduleInfo = $moduleInfo;
    }

    /**
     * @param \M2E\Core\Model\Connector\CommandInterface $command
     *
     * @return object
     * @throws \M2E\Core\Model\Exception\Connection
     * @throws \M2E\Core\Model\Exception\Connection\SystemError
     */
    public function process(\M2E\Core\Model\Connector\CommandInterface $command): object
    {
        $requestTime = \M2E\Core\Helper\Date::createCurrentGmt();

        $result = $this->sendRequest($command);

        try {
            $response = ResponseParser::parse($result);
            $response->setRequestTime($requestTime);
        } catch (\M2E\Core\Model\Exception\Connection\InvalidResponse $e) {
            throw new \M2E\Core\Model\Exception\Connection(
                (string)__(
                    'M2E Server connection failed. Find the solution <a target="_blank" href="%url">here</a>',
                    [
                        'url' => 'https://help.m2epro.com/support/solutions/articles/9000200887',
                    ],
                ),
                ['result' => $result],
            );
        }

        if ($response->getMessageCollection()->hasSystemErrors()) {
            throw new \M2E\Core\Model\Exception\Connection\SystemError(
                (string)__(
                    'Internal Server Error(s) [%error_message]',
                    ['error_message' => $response->getMessageCollection()->getCombinedSystemErrorsString()],
                ),
                $response,
            );
        }

        return $command->parseResponse($response);
    }

    /**
     * @param \M2E\Core\Model\Connector\CommandInterface $command
     *
     * @return string
     * @throws \M2E\Core\Model\Exception\Connection
     */
    private function sendRequest(\M2E\Core\Model\Connector\CommandInterface $command): string
    {
        $this->curl->setTimeout($this->config->getTimeout());
        $this->curl->setOption(CURLOPT_CONNECTTIMEOUT, $this->config->getConnectionTimeout());

        $this->curl->post(
            $this->config->getHost(),
            $this->requestBuilder->build($command, $this->protocol, $this->config, $this->moduleInfo),
        );

        return $this->curl->getBody();
    }
}
