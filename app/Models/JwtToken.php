<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @mixin IdeHelperJwtToken
 */
class JwtToken extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invalidated_at',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at'     => 'datetime',
        'invalidated_at' => 'datetime',
    ];
    
    
    /* ------------------------------------------------------------------------------------------------------------------------------ RELATIONS -+- */
    /**
     * @return BelongsTo|User
     */
    public function user(): BelongsTo|User
    {
        return $this->belongsTo(User::class);
    }
    
    /* --------------------------------------------------------------------------------------------------------------------------------- SCOPES -+- */
    public function scopeWhereValid(Builder $query): Builder
    {
        return $query->whereNull('invalidated_at')->orWhere('expires_at', '<', now());
    }
    
    public function scopeWhereFresh(Builder $query): Builder
    {
        return $query->orWhere('expires_at', '>', now());
    }
}
