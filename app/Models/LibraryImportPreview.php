<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryImportPreview extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];
    protected $casts = ['rows' => 'array', 'errors' => 'array', 'expires_at' => 'datetime'];
}
