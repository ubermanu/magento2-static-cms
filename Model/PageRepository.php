<?php

namespace Ubermanu\StaticCms\Model;

use Magento\Cms\Model\Page;
use Magento\Framework\Exception\NoSuchEntityException;

class PageRepository extends \Magento\Cms\Model\PageRepository
{
    /**
     * @param string $identifier
     * @return Page
     * @throws NoSuchEntityException
     */
    public function getByIdentifier(string $identifier)
    {
        $page = $this->pageFactory->create();
        $page->load($identifier, 'identifier');

        if (!$page->getId()) {
            throw new NoSuchEntityException(__('Page with identifier "%1" does not exist.', $identifier));
        }

        return $page;
    }
}
