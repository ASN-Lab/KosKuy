<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        $this->forceThrowawayDatabase();

        parent::setUp();

        $database = config('database.connections.'.config('database.default').'.database');

        if ($database !== 'koskuy_db' && $database !== ':memory:') {
            throw new RuntimeException(
                "The test suite resolved its database to ".var_export($database, true).'. '.
                'Tests must run against a throwaway database; run `php artisan test`, '.
                'which reads phpunit.xml, instead of pointing at a real database.'
            );
        }
    }

    /**
     * `RefreshDatabase` runs `migrate:fresh` during `parent::setUp()`, which
     * permanently drops every table. Without this override a stray
     * `DB_DATABASE=...` in the shell or a lost `<env>` entry in phpunit.xml
     * would silently wipe `database/database.sqlite` -- the real dev data.
     *
     * The values are forced before the framework boots, because once the
     * application is created the damage is already done.
     */
    private function forceThrowawayDatabase(): void
    {
        foreach (['DB_CONNECTION' => 'mysql', 'DB_DATABASE' => 'koskuy_db'] as $key => $value) {
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}