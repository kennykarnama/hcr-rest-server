<?php

namespace Hcr\Models;

use Hcr\Models\Config;

/**
 *
 */
class WordSegmentation extends Config
{
    const WORD_SEGMENTATION_FIELD = 'word_segmentation';
    const WORD_SEGMENTATION_TAM   = 'tam';
    const WORD_SEGMENTATION_IQM   = 'iqm';

    public $fileName;
    public $outFile;
    public $args;
}
