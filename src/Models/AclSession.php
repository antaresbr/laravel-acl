<?php

namespace Antares\Acl\Models;

use Antares\Acl\Database\Factories\AclSessionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AclSession extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return AclSessionFactory::new();
    }

    protected $guarded = [];

    public $timestamps = false;

    protected $dateFormat;

    public function __construct(array $attributes = [])
    {
        $this->dateFormat = config('acl.date_format');

        parent::__construct($attributes);
    }
}
