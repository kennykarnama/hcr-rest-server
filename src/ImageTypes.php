<?php

namespace Hcr;

/**
 * Image type definition
 */
final class ImageTypes
{
    const UNDEFINED = 'original';

    const RGB_IMAGE = 'rgb';

    const GRAYSCALE_IMAGE = 'grayscale';

    const BINARY_IMAGE = 'binary';

    public function __construct()
    {
        throw new \Exception("Can't initiate", 1);
    }
}
