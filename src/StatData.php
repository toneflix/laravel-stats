<?php

namespace ToneflixCode\Stats;

class StatData
{
    /** @var \ToneflixCode\Stats\Statable */
    public $statable;

    /** @var string */
    public $title;

    /** @var null|string Any of count, max, min, avg, sum */
    public $metric;

    public function __construct(Statable $statable, string $title, ?string $metric = null)
    {
        $this->statable = $statable;

        $this->title = $title;

        $this->metric = $metric;
    }
}
