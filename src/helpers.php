<?php

namespace Hcr;

/**
 * Delete files based on mark
 * @param  string $extPattern
 * @return void
 */
function deleteFiles($extPattern)
{
    array_map("unlink", glob($extPattern));
}
