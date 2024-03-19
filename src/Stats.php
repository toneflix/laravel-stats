<?php

namespace ToneflixCode\Stats;

use ToneflixCode\Stats\Enums\Metric;
use ToneflixCode\Stats\Exceptions\InvalidStatableModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\DB;

/**
 * @example (new Stats())
 *   ->registerMetric(
 *     modelClass: new AudioStory(),
 *     metric: Metric::COUNT,
 *     period: Ranger::years('created_at', 'M Y')->fromDate(now()->subYears(1))->toDate(now())->range('1 month'),
 *     aggregateField: 'id',
 *     callback: fn ($query) => $query->where('user_id', 1),
 *     label: 'audio_stories-s'
 *    )
 *     ->registerMetric(
 *         modelClass: \App\Models\v1\User::class,
 *         metric: Metric::COUNT,
 *         period: ['from' => now()->subYears(1), 'to' => now()],
 *         aggregateField: 'id',
 *     )
 *     ->build()
 */
class Stats
{
    protected $dataset = [];

    /**
     * Register the metric system
     *
     * @param  Model|string  $modelClass  A model or a model class string
     * @param  string  $aggregateField  The field to be aggregated
     * @param  Ranger|array|null  $period  The period to be aggregated
     *                                     (if using an array should contain the [from] and [to] entries)
     * @param  string  $dateField  The database field that holds the date column (Default: created_at)
     * @param  callable|null  $callback  A callback function to add restraints to your query using query builder
     * @param  string  $label  Label for the metric, by default it will be the snake cased pluralised model name
     * @return Stats
     */
    public function registerMetric(
        Model|string $modelClass,
        Metric $metric = Metric::COUNT,
        Ranger|array|null $period = null,
        string $aggregateField = 'id',
        string $dateField = 'created_at',
        ?callable $callback = null,
        ?string $label = null,
    ): \ToneflixCode\Stats\Stats {
        $model = $this->getModel($modelClass);

        /** @var \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder|Model */
        $query = $model;

        if ($callback) {// && ($callback($model) instanceof Builder || $callback($model) instanceof QueryBuilder)) {
            /** @var \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder|Model */
            $query = $callback($model);
        }

        if ($period instanceof Ranger) {
            $to = $period->getTo();
            $from = $period->getFrom();
            $grouper = $period->getGrouper();
        } elseif (isset($period['from'], $period['to'])) {
            $to = $period['to'];
            $from = $period['from'];
        }

        $build = $query
            ->selectRaw("{$metric->value}($aggregateField) as {$metric->value}, $dateField")
            ->when(isset($grouper), fn ($q) => $q->selectRaw("$grouper as grouper"))
            ->orderBy($aggregateField, 'asc')
            ->when(isset($from, $to), fn ($q) => $q->whereBetween($dateField, [$from, $to]))
            ->when(isset($grouper), fn ($q) => $q->groupBy('grouper'));
            // ->when(isset($grouper), fn ($q) => $q->groupBy($dateField));
            // ->when(! isset($grouper), fn ($q) => $q->groupBy($dateField));
            // dd($build->count());
        if ($period instanceof Ranger) {
            $format = $period->getFormat();
            $data = $build->get()->groupBy(function ($val) use ($format) {
                return $val->created_at->format($format);
            });

            $data = $period->get($data, null, $metric->value);
        } else {
            $data = $build->first($metric->value)?->{$metric->value} ?? 0;
        }

        $this->dataset[$model->getType($label)] = $data;

        return $this;
    }

    /**
     * @param  Model|string  $modelClass  A model or a model class string
     * @return \Illuminate\Database\Eloquent\Model
     *
     * @throws \ToneflixCode\Stats\Exceptions\InvalidStatableModel
     */
    public function getModel(Model|string $modelClass)
    {
        if (! is_subclass_of($modelClass, Model::class)) {
            throw InvalidStatableModel::notAModel($modelClass);
        }

        if (! is_subclass_of($modelClass, Statable::class)) {
            throw InvalidStatableModel::modelDoesNotImplementStatable($modelClass);
        }

        if ($modelClass instanceof Model) {
            return $modelClass;
        }

        /** @var Illuminate\Database\Eloquent\Model $model */
        $model = app($modelClass);

        return $model;
    }

    public function getDataSet(): array
    {
        return $this->dataset;
    }

    public function build(?User $user = null): \Illuminate\Support\Collection
    {
        return $this->perform($user);
    }

    public function perform(?User $user = null): \Illuminate\Support\Collection
    {
        return collect($this->getDataSet());
    }
}