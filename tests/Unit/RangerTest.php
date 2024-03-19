<?php

namespace ToneflixCode\Stats\Tests\Unit;

use ToneflixCode\Stats\Ranger;

foreach (
    [
        ["hours", "strftime('%H', created_at)", "HOURS(created_at)"],
        ["day", "strftime('%d', created_at)", "DAY(created_at)"],
        ["week", "strftime('%W', created_at)", "WEEK(created_at)"],
        ["month", "strftime('%m', created_at)", "MONTH(created_at)"],
        ["year", "strftime('%Y', created_at)", "YEAR(created_at)"],
    ] as $period
) {
    test("Ranger should return  the correct grouper for {$period[0]}", function () use ($period) {
        $connection = config('database.default');
        $dbDriver = config("database.connections.{$connection}.driver");

        $method = str($period[0])->plural()->toString();
        $ranger = Ranger::$method('created_at', 'M Y')->fromDate(now()->subYears(1))->toDate(now())->range('1 month');

        if ($dbDriver === 'sqlite') {
            expect($ranger->getGrouper())->toBe($period[1]);
        } else {
            expect($ranger->getGrouper())->toBe($period[2]);
        }
    });
}

foreach ([ "hours", "day", "week", "month", "year"] as $period) {
    test("{$period}() should return an instance of \ToneflixCode\Stats\Ranger::class", function () use ($period) {

        $method = str($period)->plural()->toString();
        $ranger = Ranger::$method('created_at', 'M Y')->fromDate(now()->subYears(1))->toDate(now())->range('1 month');

        expect($ranger)->toBeInstanceOf(Ranger::class);
    });
}