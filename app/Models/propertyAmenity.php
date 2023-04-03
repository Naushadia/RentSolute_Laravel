<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class propertyAmenity extends Model
{
    use HasFactory;

    protected $table = 'property_amenities';

    protected $fillable = ['amenityId','propertyId'];

    public function property()
    {
        return $this->belongsTo(property::class);
    }

    public function amenity()
    {
        return $this->belongsTo(amenity::class);
    }

}
