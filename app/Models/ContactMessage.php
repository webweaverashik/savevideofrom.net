<?php

declare (strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = ['name', 'email', 'subject', 'message', 'ip_hash', 'is_read'];

    protected function casts(): array
    {
        return ['is_read' => 'boolean'];
    }
}
