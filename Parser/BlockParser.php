<?php

namespace Ubermanu\StaticCms\Parser;

use Magento\Cms\Model\Block;
use Magento\Cms\Model\BlockFactory;
use Mni\FrontYAML\Parser as FrontYAMLParser;

class BlockParser extends AbstractParser
{
    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * @var FrontYAMLParser
     */
    protected $frontYAMLParser;

    public function __construct(
        BlockFactory $blockFactory,
        FrontYAMLParser $frontYAMLParser
    ) {
        $this->blockFactory = $blockFactory;
        $this->frontYAMLParser = $frontYAMLParser;
    }

    /**
     * @param string $content
     * @return Block
     */
    public function parse(string $content): Block
    {
        $front = $this->frontYAMLParser->parse($content, false);
        $additionalData = $front->getYAML();

        $block = $this->blockFactory->create();
        $block->addData($additionalData);
        $block->setContent($front->getContent());

        return $block;
    }
}
