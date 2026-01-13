<?php

namespace Modules\User\Models;

use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder; // Added this import
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Support\UsernameGenerator;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

/**
 * Represents a user in the system.
 *
 * @property int|string $id
 * @property string $name
 * @property string $email
 * @property string $username
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property-read string|null $remember_token
 * @property-read \Illuminate\Support\Carbon|null $created_at
 * @property-read \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read \Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property string|null $avatar_url
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static> owner()
 */
class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    use HasFactory;
    use HasRoles;
    use InteractsWithMedia;
    use MustVerifyEmailTrait;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
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
     * Create a new Eloquent model instance.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (config('user.type_id') === 'uuid') {
            $this->incrementing = false;
            $this->keyType = 'string';
        }
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (config('user.type_id') === 'uuid' && empty($user->id)) {
                $user->id = (string) Str::uuid();
            }

            if (empty($user->username)) {
                $user->username = UsernameGenerator::generate();
            }
        });
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

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

    /**
     * Get the user's initials.
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn (string $word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Register the media collections for the user's avatar.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('user_avatar')
            ->singleFile();
    }

    /**
     * Get the URL of the user's avatar.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('user_avatar') ?: null;
    }

    public function changeAvatar(string|UploadedFile $file, string $collectionName = 'user_avatar'): bool
    {
        $this->clearMediaCollection('user_avatar');

        return (bool) $this->addMedia($file)->toMediaCollection($collectionName) ?? false;
    }

    /**
     * Scope a query to only include users with the 'owner' role.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public function scopeOwner(Builder $query): Builder
    {
        return $query->role('owner');
    }
}
