<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'send_id',
        'received_id',
        'type',
        'value',
        'group_id',
    ];

    public function send(): BelongsTo
    {
        return $this->belongsTo(User::class, 'send_id');
    }

    public function received(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
