<?php

namespace Tests\Feature\Smoke;

use Tests\TestCase;

class CriticalPathsTest extends TestCase
{
    /**
     * Test mobile login page is accessible.
     */
    public function test_mobile_login_page_is_accessible(): void
    {
        $response = $this->get('/mobile/login');

        $response->assertStatus(200);
    }

    /**
     * Test mobile register page is accessible.
     */
    public function test_mobile_register_page_is_accessible(): void
    {
        $response = $this->get('/mobile/register');

        $response->assertStatus(200);
    }

    /**
     * Test admin login page is accessible.
     */
    public function test_admin_login_page_is_accessible(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
    }

    /**
     * Test welcome page is accessible.
     */
    public function test_welcome_page_is_accessible(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
