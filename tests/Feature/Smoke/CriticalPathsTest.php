<?php

namespace Tests\Feature\Smoke;

use Tests\TestCase;

class CriticalPathsTest extends TestCase
{
    /**
     * Test admin login page is accessible.
     */
    public function test_example(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
    }
}
