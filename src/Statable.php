<?php

namespace ToneflixCode\Stats;

/**
 * @method_ int count(string $field)
 * @method_ int min(string $field)
 * @method_ int max(string $field)
 * @method_ int avg(string $field)
 * @method_ int sum(string $field)
 */
interface Statable
{
    public function getStats(): StatData;
}
