<?php

namespace Hcr;

use Hcr\ImageTypes;

/**
 *
 */
class Image
{
    private $path;

    private $imageType;

    /**
     * Constructor
     * @param string $path
     * @param string $imageType
     */
    public function __construct($path = '', $imageType = ImageTypes::UNDEFINED)
    {
        $this->path = $path;

        $this->imageType = $imageType;
    }
    /**
     * Get data
     * @return array<string>
     */
    public function getData()
    {
        return array(
            'path'=>$this->path,
            'imageType'=>$this->imageType
        );
    }
    /**
     * Get path
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
    /**
     * Get image type
     * @return string
     */
    public function getImageType()
    {
        return $this->imageType;
    }
}
