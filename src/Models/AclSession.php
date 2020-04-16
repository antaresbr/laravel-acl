<?php

namespace Antares\Acl\Models;

use Illuminate\Database\Eloquent\Model;

class AclSession extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    protected $dateFormat;

    public function __construct(array $attributes = []) {
        $this->dateFormat = config('acl.date_format');

        parent::__construct($attributes);
    }
}
