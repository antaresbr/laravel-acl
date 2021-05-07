<?php

namespace Antares\Acl\Tests\Models;

use Antares\Acl\Models\User as Authenticatable;

class User extends Authenticatable
{
    public function active()
    {
        $this->active = true;
        $this->save();
        return $this;
    }

    public function inactive()
    {
        $this->active = false;
        $this->save();
        return $this;
    }

    public function block()
    {
        $this->blocked = true;
        $this->save();
        return $this;
    }

    public function unblock()
    {
        $this->blocked = false;
        $this->save();
        return $this;
    }

    public function enable()
    {
        $this->active = true;
        $this->blocked = false;
        $this->save();
        return $this;
    }

    public function disable()
    {
        $this->active = false;
        $this->blocked = true;
        $this->save();
        return $this;
    }
}
