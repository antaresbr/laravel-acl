<?php

namespace Antares\Acl\Tests\Feature;

use Antares\Acl\Tests\TestCase;

class AliveTest extends TestCase
{
    /** @test */
    public function get_alive()
    {
        $response = $this->get(config('acl.route.prefix.api') . '/alive');
        $response->assertStatus(200);

        $json = $response->json();
        $this->assertArrayHasKey('env', $json);
        $this->assertArrayHasKey('version', $json);
        $this->assertArrayHasKey('serverDateTime', $json);
    }
}
