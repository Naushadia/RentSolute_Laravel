<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class image extends Model
{
    use HasFactory;

    protected $table = 'images';

    protected $primaryKey = 'id';

    protected $fillable = ['caption','UserId','image','filename'];

    public function properties()
    {
        return $this->hasManyThrough(
            property::class,
            propertyImages::class,
            'imageId',
            'id',
            'id',
            'propertyId',
        );
    }

    public function manyRooms()
    {
        return $this->hasMany(ROOM::class, 'imageId', 'id');

        // foreign key column on the related model that references the local key on the current model, local key column on the current model.
    }
}
