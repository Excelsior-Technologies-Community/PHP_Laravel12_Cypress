<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestRun extends Model
{
    use HasFactory;

    protected $fillable = [
        'spec_name',
        'test_name',
        'status',
        'browser',
        'duration',
        'error_message',
        'executed_at',
    ];

    protected function casts(): array
    {
        return [
            'executed_at' => 'datetime',
            'duration' => 'integer',
        ];
    }
}