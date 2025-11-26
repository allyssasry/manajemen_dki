<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use Notifiable; 

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
            'first_name',
        'last_name',
        'name',
        'username',
        'email',
        'phone',
        'gender',
        'address',
        'role',
        'password',
         'avatar',          // <— tambahkan agar bisa mass assign
        'avatar_path',     // kalau sebelumnya pakai ini, biarin
        'photo',   
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
         'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
 protected $appends = [
        'avatar_url_public',
    ];
    // app/Models/User.php
   public function getAvatarUrlPublicAttribute(): ?string
    {
        foreach (['avatar','avatar_path','photo','profile_photo_path'] as $col) {
            $p = $this->{$col} ?? null;
            if (!$p) continue;

            // jika file ada di storage public, buat url-nya
            $url = Storage::disk('public')->url($p);

            // tambahkan versi agar cache browser dibypass
            try {
                $ver = Storage::disk('public')->exists($p)
                    ? Storage::disk('public')->lastModified($p)
                    : ($this->updated_at?->timestamp ?? time());
            } catch (\Throwable $e) {
                $ver = $this->updated_at?->timestamp ?? time();
            }

            // jika url sudah punya query, pakai &k=..., kalau tidak ?k=...
            $join = str_contains($url, '?') ? '&' : '?';
            return $url . $join . 'k=' . $ver;
        }
        return null;
    }

    /** Tambahan kecil kalau mau dipakai di Blade sebagai key versi. */
    public function getAvatarCacheKeyAttribute(): int
    {
        return (int) ($this->updated_at?->timestamp ?? time());
    }      public function getAvatarUrlAttribute($value): ?string
    {
        if (!$value) return $this->avatar_url_public;

        if (Str::startsWith($value, ['http://', 'https://', 'data:image'])) {
            return $value;
        }

        if (Storage::disk('public')->exists($value)) {
            return Storage::url($value);
        }

        // fallback ke avatar_url_public (mis. kalau kolom yg dipakai avatar_path)
        return $this->avatar_url_public;
    }
}
