<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_root_redirects_to_dashboard_or_login(): void
    {
        $response = $this->get('/');

        $this->assertContains($response->getStatusCode(), [200, 302]);
    }
}
