<?php

declare(strict_types=1);

namespace ToneflixCode\Stats\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ToneflixCode\Stats\LaravelStatsServiceProvider;
use ToneflixCode\Stats\Tests\Database\Factories\UserFactory;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    protected $factories = [
        UserFactory::class,
    ];

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelStatsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        config()->set('app.key', 'base64:EWcFBKBT8lKlGK8nQhTHY+wg19QlfmbhtO9Qnn3NfcA=');
        config()->set('database.default', 'testing');

        $migration = include __DIR__ . '/database/migrations/0_create_users_tables.php';
        $migration->up();
        $migration = include __DIR__ . '/database/migrations/1_create_posts_tables.php';
        $migration->up();
    }
}