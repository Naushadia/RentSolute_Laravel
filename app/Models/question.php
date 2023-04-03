<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class question extends Model
{
    use HasFactory;

    protected $table = 'questions';

    protected $primaryKey = 'id';

    protected $fillable = ['title','type','has_other','UserId'];

    public function options()
    {
        return $this->hasMany(option::class, 'questionId','id');
    }

    public function properties()
    {
        return $this->hasManyThrough(
            property::class,
            propertyQuestion::class,
            'questionId',
            'id',
            'id',
            'propertyId',
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
