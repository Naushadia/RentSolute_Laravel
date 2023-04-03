<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class option extends Model
{
    use HasFactory;

    protected $table = 'options';

    protected $primaryKey = 'id';

    protected $fillable = ['text', 'preferred','questionId'];

    public function question()
    {
        return $this->belongsTo(question::class, 'questionId');
    }
}
