<?php

namespace Hcr;

use Hcr\ImageProcessor;
use Hcr\Models\Config;

/**
 *
 */
class RecognitionSystem
{
    private $imageProcessor;
    /**
     * Default constructor
     * @param ImageProcessor $imageProcessor
     */
    public function __construct(ImageProcessor $imageProcessor)
    {
        $this->imageProcessor = $imageProcessor;
    }
    /**
     * Recognize image contains handwritten
     * characters
     * @param  Config $config
     * @param  array  $args
     * @return mixed
     */
    public function recognize(Config $config, array $args)
    {
    }
}
