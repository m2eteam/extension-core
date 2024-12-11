<?php

declare(strict_types=1);

namespace M2E\Core\Helper\Magento;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Asset\Repository;

class Assets
{
    private Repository $assetsRepo;
    private RequestInterface $request;

    public function __construct(
        Repository $assetsRepo,
        RequestInterface $request
    ) {
        $this->assetsRepo = $assetsRepo;
        $this->request = $request;
    }

    public function getViewFileUrl(string $fileName): string
    {
        return $this->assetsRepo->getUrlWithParams($fileName, ['_secure' => $this->request->isSecure()]);
    }
}
