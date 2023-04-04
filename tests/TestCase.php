<?php

namespace Tests;

use App\Models\Bus;
use App\Models\User;
use App\Models\City;
use App\Models\Trip;
use App\Models\TripStation;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    protected function beforeRefreshingDatabase()
    {
        Config::set('database.default', 'sqlite_testing');
        Config::set('app.env', 'testing');
    }
}
