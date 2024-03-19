<?php

namespace ToneflixCode\Stats\Exceptions;

use Exception;

class InvalidStatableModel extends Exception
{
    public static function notAModel(string $model): self
    {
        return new self("Class `{$model}` is not an Eloquent model.");
    }

    public static function modelDoesNotImplementStatable(string $model): self
    {
        return new self("Model `{$model}` does not implement the `ToneflixCode\Stats\Statable` interface.");
    }
}