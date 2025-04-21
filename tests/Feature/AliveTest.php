<?php

namespace Antares\Acl\Tests\Feature;

use Antares\Acl\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AliveTest extends TestCase
{
    #[Test]
    public function get_alive()
    {
        $response = $this->get(config('acl.route.prefix.api') . '/alive');
        $response->assertStatus(200);

        $json = $response->json();
        $this->assertArrayHasKey('package', $json);
        $this->assertArrayHasKey('env', $json);
        $this->assertArrayHasKey('serverDateTime', $json);
    }
}
