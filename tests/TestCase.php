<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
require_once __DIR__ . '/polyfill.php';

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
}
