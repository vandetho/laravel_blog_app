<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $table = 'blogs';

    public $timestamps = true;

    protected $fillable = [
        'title',
        'content',
        'state'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'title' => 'string',
        'content' => 'string',
        'state' => 'string'
    ];

    public function setState(string $value): void
    {
        $this->attributes['state'] = $value;
    }

    public function getState(): string|null
    {
        return $this->attributes['state'] ?? null;
    }
}
