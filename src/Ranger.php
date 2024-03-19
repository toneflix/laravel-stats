<?php

namespace ToneflixCode\Stats;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Ranger
{
    protected \Carbon\Carbon|string $to;

    protected \Carbon\Carbon|string $from;

    protected string $range;

    protected string $format;

    protected string $dbDriver;

    protected bool $flat;

    protected \Illuminate\Database\Query\Expression|string $grouper;

    public function __construct(
        \Carbon\Carbon|string $from = 'now',
        \Carbon\Carbon|string $to = 'now + 1 day',
        $range = '1 day',
        $flat = true,
    ) {
        $this->to = $to;
        $this->flat = $flat;
        $this->from = $from;
        $this->range = $range;

        $connection = config('database.default');
        $this->dbDriver = config("database.connections.{$connection}.driver");
    }

    public function toDate(\Carbon\Carbon|string $to): self
    {
        $this->to = $to;

        return $this;
    }

    public function fromDate(\Carbon\Carbon|string $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function flat(bool $flat): self
    {
        $this->flat = $flat;

        return $this;
    }

    public function range(string $range): self
    {
        $this->range = $range;

        return $this;
    }

    public static function hours($field = 'created_at', $format = 'H'): self
    {
        $instance = new self();
        $instance->format = $format;
        if ($instance->dbDriver === 'sqlite') {
            $instance->grouper = "strftime('%H', $field)";
        } else {
            $instance->grouper = "HOUR($field)";
        }

        return $instance;
    }

    public static function days($field = 'created_at', $format = 'l'): self
    {
        $instance = new self();
        $instance->format = $format;
        if ($instance->dbDriver === 'sqlite') {
            $instance->grouper = "strftime('%d', $field)";
        } else {
            $instance->grouper = "DAY($field)";
        }

        return $instance;
    }

    public static function weeks($field = 'created_at', $format = 'W'): self
    {
        $instance = new self();
        $instance->format = $format;
        if ($instance->dbDriver === 'sqlite') {
            $instance->grouper = "strftime('%W', $field)";
        } else {
            $instance->grouper = "WEEK($field)";
        }

        return $instance;
    }

    public static function months($field = 'created_at', $format = 'M'): self
    {
        $instance = new self();
        $instance->format = $format;
        if ($instance->dbDriver === 'sqlite') {
            $instance->grouper = "strftime('%m', $field)";
        } else {
            $instance->grouper = "MONTH($field)";
        }

        return $instance;
    }

    public static function years($field = 'created_at', $format = 'Y'): self
    {
        $instance = new self();
        $instance->format = $format;
        if ($instance->dbDriver === 'sqlite') {
            $instance->grouper = "strftime('%Y', $field)";
        } else {
            $instance->grouper = "YEAR($field)";
        }

        return $instance;
    }

    /**
     * Build the range data
     *
     * @return Collection
     */
    public function get(array|Collection|EloquentCollection $data, ?string $format = null, string $key = 'total')
    {
        $all_time = collect();
        $period = \Carbon\CarbonPeriod::create($this->from, $this->range, $this->to);

        foreach ($period as $p) {
            $m = $p->format($format ?? $this->format);

            if ($this->flat) {
                if (isset($data[$m])) {
                    $all_time[$m] = $data[$m]->first()->{$key} ?? 0;
                } else {
                    $all_time[$m] = 0;
                }
            } else {
                if (isset($data[$m])) {
                    $all_time->push([
                        'label' => $m,
                        'value' => $data[$m]->first()->{$key} ?? 0,
                    ]);
                } else {
                    // $all_time[$m] = 0;
                    $all_time->push([
                        'label' => $m,
                        'value' => 0,
                    ]);
                }
            }
        }

        return $all_time;
    }

    public function getGrouper(): \Illuminate\Database\Query\Expression|string
    {
        return $this->grouper;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getFrom(): \Carbon\Carbon|string
    {
        return $this->from;
    }

    public function getTo(): \Carbon\Carbon|string
    {
        return $this->to;
    }
}