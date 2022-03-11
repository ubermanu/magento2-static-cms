<?php

namespace Ubermanu\StaticCms\Model;

use Magento\Cms\Model\Block;
use Magento\Framework\Exception\NoSuchEntityException;

class BlockRepository extends \Magento\Cms\Model\BlockRepository
{
    /**
     * @param string $identifier
     * @return Block
     * @throws NoSuchEntityException
     */
    public function getByIdentifier(string $identifier)
    {
        $block = $this->blockFactory->create();
        $block->load($identifier, 'identifier');

        if (!$block->getId()) {
            throw new NoSuchEntityException(__('Block with identifier "%1" does not exist.', $identifier));
        }

        return $block;
    }
}
