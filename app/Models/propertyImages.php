<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class propertyImages extends Model
{
    use HasFactory;

    protected $table = 'property_images';

    protected $fillable = ['imageId','propertyId'];

    public function property()
    {
        return $this->belongsTo(property::class);
    }

    public function image()
    {
        return $this->belongsTo(image::class);
    }
}
