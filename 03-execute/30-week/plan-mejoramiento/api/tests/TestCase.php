<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $app = parent::createApplication();
        $config = $app['config'];
        if ($config->get('database.default') !== 'pgsql'
            || $config->get('database.connections.pgsql.database') !== 'toy_library_test'
            || $config->get('database.connections.pgsql.url')) {
            throw new RuntimeException('Las pruebas requieren la base PostgreSQL aislada toy_library_test y no admiten DB_URL.');
        }
        return $app;
    }
}
