<?php

namespace ToneflixCode\Stats;

use Illuminate\Support\Str;

trait HasStats
{
    public function getStats(): StatData
    {
        return new StatData($this, 'Media');
    }

    public function getType(?string $defined = null): string
    {
        if ($defined) {
            return $defined;
        }

        $className = class_basename(static::class);

        $type = Str::before($className, 'hasStats');

        $type = Str::snake(Str::plural($type));

        return Str::plural($type);
    }
}
