<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_staff_login_page_loads(): void
    {
        $this->withoutVite()->get('/masuk')->assertOk()->assertSee('Paket aman.');
    }
}
