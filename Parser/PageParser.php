<?php

namespace Ubermanu\StaticCms\Parser;

use Magento\Cms\Model\Page;
use Magento\Cms\Model\PageFactory;
use Mni\FrontYAML\Parser as FrontYAMLParser;

class PageParser extends AbstractParser
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var FrontYAMLParser
     */
    protected $frontYAMLParser;

    public function __construct(
        PageFactory $pageFactory,
        FrontYAMLParser $frontYAMLParser
    ) {
        $this->pageFactory = $pageFactory;
        $this->frontYAMLParser = $frontYAMLParser;
    }

    /**
     * @param string $content
     * @return Page
     */
    public function parse(string $content): Page
    {
        $front = $this->frontYAMLParser->parse($content, false);
        $additionalData = $front->getYAML() ?? [];

        $page = $this->pageFactory->create();
        $page->addData($additionalData);
        $page->setContent($front->getContent());

        return $page;
    }
}
