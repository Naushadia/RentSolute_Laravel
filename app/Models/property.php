<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class property extends Model
{
    use HasFactory;

    protected $table = 'properties';

    protected $primaryKey = 'id';

    protected $fillable = ['name','property_type','description','tenancy_status','street','city','state','postal_code','country','latitude','longitude','furnishing_status','furnishing_details','share_property_url','area','userId'];

    public function property_amenities()
    {
        return $this->hasManyThrough(
            amenity::class,
            propertyAmenity::class,
            'propertyId', // foreign key column on the intermediate model that references the local key on the current model
            'id',  // foreign key column on the final model that references the local key on the intermediate model
            'id', //  local key column on the current model
            'amenityId', // local key column on the intermediate model
        );
    }

    public function property_images()
    {
        return $this->hasManyThrough(
            image::class,
            propertyImages::class,
            'propertyId',
            'id',
            'id',
            'imageId',
        );
    }

    public function property_questions()
    {
        return $this->hasManyThrough(
            question::class,
            propertyQuestion::class,
            'propertyId',
            'id',
            'id',
            'questionId',
        );
    }

    public function property_question_options()
    {
        return $this->hasManyThrough(
            option::class,
            propertyQuestion::class,
            'propertyId', // foreign key intermediate
            'questionId', // foreign key final
            'id', // local key current
            'questionId', // local key intermediate
        );
    }

    public function rooms()
    {
        return $this->hasMany(ROOM::class, 'propertyId','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
