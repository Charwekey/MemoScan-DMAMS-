<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Support\Facades\Auth;

#[Fillable([
    'user_id',
    'action',
    'target_id',
    'details'
])]
class AuditLog extends Model
{
    use HasFactory;

    // Turn off standard timestamps, since we only have created_at
    public $timestamps = false;

    /**
     * Get the user who performed the action.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Static helper to record an audit log entry.
     */
    public static function log(string $action, ?int $targetId = null, ?string $details = null): self
    {
        return self::create([
            'user_id' => Auth::id(), // Automatically grab the authenticated user, or null if guest
            'action' => $action,
            'target_id' => $targetId,
            'details' => $details,
            'created_at' => now(),
        ]);
    }
}
