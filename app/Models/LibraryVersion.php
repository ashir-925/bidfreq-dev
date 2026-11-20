<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryVersion extends Model
{
    protected $guarded = [];
    protected $casts = ['summary' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
