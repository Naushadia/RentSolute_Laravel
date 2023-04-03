<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class propertyQuestion extends Model
{
    use HasFactory;

    protected $table = 'property_questions';

    protected $fillable = ['questionId','propertyId','optionId','preferred'];

    public function property()
    {
        return $this->belongsTo(property::class);
    }

    public function option()
    {
        return $this->belongsTo(option::class);
    }
}
