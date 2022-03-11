<?php

namespace Ubermanu\StaticCms\Api;

interface ParserInterface
{
    /**
     * @param string $content
     * @return mixed
     */
    public function parse(string $content);

    /**
     * @param string $file
     * @return mixed
     */
    public function parseFile(string $file);
}
