<?php

namespace Ubermanu\StaticCms\Parser;

use Ubermanu\StaticCms\Api\ParserInterface;

abstract class AbstractParser implements ParserInterface
{
    /**
     * @inheritDoc
     */
    abstract function parse(string $content);

    /**
     * @inheritDoc
     */
    public function parseFile(string $file)
    {
        return $this->parse(file_get_contents($file));
    }
}
