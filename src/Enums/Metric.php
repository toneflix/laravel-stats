<?php

namespace ToneflixCode\Stats\Enums;

enum Metric: string
{
    case MIN = 'min';
    case MAX = 'max';
    case AVG = 'avg';
    case SUM = 'sum';
    case COUNT = 'count';
}
