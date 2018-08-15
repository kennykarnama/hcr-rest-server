<?php

namespace Hcr\Models;

use Hcr\Models\Config;

/**
 *
 */
class CharacterSegmentation extends Config
{
    const CHARACTER_SEGMENTATION_FIELD = 'character_segmentation';
    
    public $fileName;
    public $outFile;
    public $args;
}
