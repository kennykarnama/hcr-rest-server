<?php

namespace Hcr\Interfaces;

interface StreamInterface
{

    /**
     * Read data in
     * @param  string $path
     * @param  mixed
     * @return mixed
     */
    public function in($path, $options);
    /**
     * Write data out
     * @param string $path
     * @param mixed $content
     * @param mixed $options
     * @return mixed
     */
    public function out($path, $content, $options);

    /**
     * Process stream
     * @param  mixed $content
     * @param  mixed $options
     * @return mixed
     */
    public function process($content, $options);
}
