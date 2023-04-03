<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class amenity extends Model
{
    use HasFactory;

    protected $table = 'amenities';

    protected $primaryKey = 'id';

    protected $fillable = ['name','icon','userId'];

    protected $casts = [
        'icon' => 'array'
      ];

    public function properties()
    {
        return $this->hasManyThrough(
            property::class, // Final Model
            propertyAmenity::class,  // Intermediate Model
            'amenityId', // foreign key column on the intermediate model that references the local key on the current model
            'id', // foreign key column on the final model that references the local key on the intermediate model
            'id', // local key column on the current model
            'propertyId' // local key column on the intermediate model
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
