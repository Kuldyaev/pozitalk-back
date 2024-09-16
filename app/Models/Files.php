<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    use HasFactory;
    protected $fillable=[
        'path'
    ];

    public function toArray()
    {
        $path = str_replace('public', 'storage', $this->getAttribute('path'));
        $this->setAttribute('path', config('app.url').'/'.$path);

        return parent::toArray();
    }
}
