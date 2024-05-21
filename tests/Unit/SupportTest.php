<?php declare(strict_types=1);

namespace Antares\Acl\Tests\Unit;

use Antares\Acl\Tests\TestCase;

final class SupportTest extends TestCase
{
    private function getPath()
    {
        return ai_acl_path();
    }

    private function getInfos()
    {
        return ai_acl_infos();
    }

    public function testHelpers()
    {
        $path = $this->getPath();
        $this->assertIsString($path);
        $this->assertEquals(substr(__DIR__, 0, strlen($path)), $path);

        $infos = $this->getInfos();
        $this->assertIsObject($infos);
    }

    public function testInfos()
    {
        $infos = $this->getInfos();
        $this->assertObjectHasProperty('name', $infos);
        $this->assertObjectHasProperty('version', $infos);
        $this->assertObjectHasProperty('major', $infos->version);
        $this->assertObjectHasProperty('release', $infos->version);
        $this->assertObjectHasProperty('minor', $infos->version);
    }
}
