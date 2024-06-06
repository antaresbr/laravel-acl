<?php

namespace Antares\Acl\Tests\Unit;

use Antares\Acl\Tests\TestCase;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use stdClass;

class JwtTest extends TestCase
{
    private $payload = [
        'iss' => 'Test Issuer',
        'sub' => 'Laravel ACL Package',
        'website' => 'localhost.local',
        'sid' => 123,
        'user' => 321,
        'issued_at' => '2020-03-01 12:00:00.000000',
        'expires_at' => '2020-03-01 16:00:00.000000',
    ];

    /** @test */
    public function create_and_validate_jwt_token()
    {
        $token = JWT::encode($this->payload, config('acl.jwt.key'), config('acl.jwt.alg'));
        $this->assertIsString($token, 'message here');

        $token_pieces = explode('.', $token);
        $this->assertCount(3, $token_pieces);

        //$headers = new stdClass();
        $decoded = JWT::decode($token, new Key(config('acl.jwt.key'), config('acl.jwt.alg')));
        $this->assertEquals(json_encode($this->payload), json_encode($decoded));
    }
}
