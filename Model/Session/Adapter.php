<?php

declare(strict_types=1);

namespace M2E\Core\Model\Session;

class Adapter
{
    private string $extensionName;
    private \Magento\Framework\Session\SessionManager $session;

    public function __construct(
        string $extensionName,
        \Magento\Framework\Session\SessionManager $session
    ) {
        $this->extensionName = $extensionName;
        $this->session = $session;
    }

    // ----------------------------------------

    /**
     * @param string $key
     * @param bool $clear
     *
     * @return mixed
     */
    public function getValue($key, $clear = false)
    {
        return $this->session->getData(
            $this->extensionName . '_' . $key,
            $clear
        );
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function setValue($key, $value): void
    {
        $this->session->setData($this->extensionName . '_' . $key, $value);
    }

    // ---------------------------------------

    /**
     * @return array
     */
    public function getAllValues(): array
    {
        $return = [];
        $session = $this->session->getData();

        foreach ($session as $key => $value) {
            if (strpos($key, $this->extensionName) === 0) {
                $tempReturnedKey = substr($key, strlen($this->extensionName) + 1);
                $return[$tempReturnedKey] = $this->session->getData($key);
            }
        }

        return $return;
    }

    /**
     * @param string $key
     *
     * @return void
     */
    public function removeValue($key): void
    {
        $this->session->getData($this->extensionName . '_' . $key, true);
    }

    /**
     * @return void
     */
    public function removeAllValues(): void
    {
        $session = $this->session->getData();

        foreach ($session as $key => $value) {
            if (strpos($key, $this->extensionName) === 0) {
                $this->session->getData($key, true);
            }
        }
    }
}
