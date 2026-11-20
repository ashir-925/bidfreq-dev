<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryTaskChange extends Model
{
    protected $guarded = [];
    protected $casts = ['before' => 'array', 'after' => 'array'];

    public function version()
    {
        return $this->belongsTo(LibraryVersion::class, 'library_version_id');
    }
}
