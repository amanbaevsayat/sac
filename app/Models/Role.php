<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'title',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public static function findByCode(string $code)
    {
        return self::firstWhere('code', $code);
    }
}
