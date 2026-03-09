<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIConversation extends Model
{
    protected $table = 'ai_conversations';

    protected $fillable = [
        'business_id',
        'session_id',
        'role',
        'content',
        'tokens',
    ];

    /**
     * Return the last $limit turns for a given business + session,
     * formatted as the Claude messages array ([{role, content}, ...]).
     */
    public static function historyFor(string $businessId, string $sessionId, int $limit = 10): array
    {
        return static::where('business_id', $businessId)
            ->where('session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get(['role', 'content'])
            ->reverse()          // oldest first for Claude
            ->values()
            ->map(fn ($row) => ['role' => $row->role, 'content' => $row->content])
            ->all();
    }
}
