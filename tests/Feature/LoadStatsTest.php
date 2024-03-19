<?php

namespace ToneflixCode\Stats\Tests\Feature;

use Illuminate\Support\Collection;
use ToneflixCode\Stats\Enums\Metric;
use ToneflixCode\Stats\Ranger;
use ToneflixCode\Stats\Stats;
use ToneflixCode\Stats\Tests\Models\Post;
use ToneflixCode\Stats\Tests\Models\User;

test('user can be loaded', function () {
    $user = User::factory()->create();

    expect($user->id)->toBe(1);
});

test('can generate stats', function () {
    User::factory(50)->create();

    $stats = (new Stats())
        ->registerMetric(
            modelClass: new User(),
            period: ['from' => now()->subYears(1), 'to' => now()],
            metric: Metric::COUNT,
            aggregateField: 'id',
        )
        ->build();

    expect($stats)->toBeInstanceOf(Collection::class);
});

test('can generate multiple stats', function () {
    User::factory(50)->create();
    Post::factory(25)->create(['user_id' => 1]);

    $stats = (new Stats())
        ->registerMetric(
            modelClass: User::class,
            metric: Metric::COUNT,
            period: Ranger::years('created_at', 'M Y')->fromDate(now()->subYears(1))->toDate(now())->range('1 month'),
            aggregateField: 'id',
            label: 'old_users'
        )
        ->registerMetric(
            modelClass: User::class,
            period: ['from' => now()->subYears(1), 'to' => now()],
            metric: Metric::COUNT,
            aggregateField: 'id',
        )
        ->registerMetric(
            modelClass: new User(),
            period: ['from' => now()->subYears(1), 'to' => now()],
            metric: Metric::COUNT,
            callback: fn ($query) => $query->find(1)->posts(),
            aggregateField: 'id',
            label: 'posts'
        )
        ->registerMetric(
            modelClass: User::class,
            metric: Metric::COUNT,
            period: Ranger::months('created_at', 'M Y')->fromDate(now()->subYears(1))->toDate(now())->range('1 month'),
            aggregateField: 'id',
            label: 'old_users_months'
        )
        ->build();

    expect(isset($stats['users']))->toBeTrue();
    expect(isset($stats['posts']))->toBeTrue();
    expect(isset($stats['old_users']))->toBeTrue();
    expect(isset($stats['old_users_months']))->toBeTrue();
});

test('can generate accurate stats', function () {
    User::factory(50)->create();

    $stats = (new Stats())
        ->registerMetric(
            modelClass: new User(),
            period: ['from' => now()->subYears(1), 'to' => now()],
            metric: Metric::COUNT,
            aggregateField: 'id',
        )
        ->build();

    expect($stats['users'])->toBe(User::count());
});