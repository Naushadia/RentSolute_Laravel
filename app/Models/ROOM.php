<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ROOM extends Model
{
    use HasFactory;

    protected $table = 'r_o_o_m_s';

    protected $primaryKey = 'id';

    protected $fillable = ['name','url','caption','room_type','imageId','propertyId'];

    public function image()
    {
        return $this->belongsTo(image::class);
    }

    public function property()
    {
        return $this->belongsTo(property::class);
    }
}
