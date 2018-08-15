<?php

namespace Hcr\Models;

use Hcr\Models\Config;

/**
 *
 */
class LineSegmentation extends Config
{
    const LINE_SEGMENTATION_FIELD = 'line_segmentation';


    public $fileName;
    public $outFile;
    public $args;
}
