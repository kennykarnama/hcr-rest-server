<?php

namespace Hcr;

use Hcr\Interfaces\StreamInterface;

/**
 * Image Stream Class
 */
class ImageStream implements StreamInterface
{
    const EXTENSION_FIELD = 'ext';

    const ALLOWED_IMAGE_EXTENSIONS = array(
        '.png'
    );
    /**
     * @inheritDoc
     */
    public function in($path, $options = array())
    {
        return $this->getImage(
            $path,
            $this->filterImageExtension(
                $options[self::EXTENSION_FIELD]
            )
        );
    }
    /**
     * @inheritDoc
     */
    public function out($path, $content, $options)
    {
        return $this->writeImage(
            $content,
            $path,
            $this->filterImageExtension(
                $options[self::EXTENSION_FIELD]
            )
        );
    }
    /**
     * @inheritDoc
     */
    public function process($content, $options)
    {
        return $content;
    }
    /**
     * Create image
     * @param  string $path
     * @param  string $extension
     * @return resource
     */
    private function getImage($path, $extension)
    {
        if ($extension == ".png") {
            $img = imagecreatefrompng($path);

            if (!$img) {
                throw new \Exception("Image not found", 1);
            }
            return $img;
        }
    }
    /**
     * Write an image to file
     * @param  resource $img
     * @param  string $to
     * @param  string $extension
     * @return bool
     */
    private function writeImage($img, $to, $extension)
    {
        if ($extension == ".png") {
            //header("Content-Type: image/png");
            return imagepng($img, $to);
        }
    }

    /**
     * Filter image extensions
     * @param  string $extension
     * @return string
     * @throws \Exception
     */
    private function filterImageExtension($extension)
    {
        if (in_array($extension, self::ALLOWED_IMAGE_EXTENSIONS)) {
            return $extension;
        }
        throw new \Exception("Unsupported Image extension", 1);
    }
}
