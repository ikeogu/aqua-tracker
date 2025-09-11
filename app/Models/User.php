<?php

namespace App\Models;

use App\Enums\Otp;
use App\Enums\Role;
use App\Notifications\EmailVerificationNotification;
use App\Notifications\ForgotPasswordNotification;
use App\Traits\HasOtp;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class User extends Authenticatable implements HasMedia
{
    use HasFactory, Notifiable, InteractsWithMedia, HasOtp, HasUuids, HasRoles, HasApiTokens, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'fully_onboarded',
        'team_member_onboarded',
        'tenant_id',
        'telephone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $guard_name = 'web';
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
            'fully_onboarded' => 'boolean',
            'team_member_onboarded' => 'boolean'
        ];
    }

    protected $with = ['tenants'];
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_picture')->singleFile();
    }

    /** @codeCoverageIgnore */
    public function role(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getRoleNames()[0] ?? null
        );
    }

    /** @codeCoverageIgnore */
    public function isVerified(): Attribute
    {
        return Attribute::make(
            get: fn() => !is_null($this->email_verified_at),
        );
    }

    public function isAdmin(): bool
    {
        return $this->hasRole([Role::SUPER_ADMIN->value]);
    }

    public function isCreator(): bool
    {
        return $this->hasAnyRole([Role::ORGANIZATION_OWNER->value, Role::FARM_TEAM_OWNER->value]);
    }

    public function isUser(): bool
    {
        return $this->hasRole([Role::FARM_EMPLOYEE->value]);
    }


    public function sendEmailVerificationOtp(): void
    {
        $this->generateOtpFor(Otp::EMAIL_VERIFICATION);
        $this->notify(new EmailVerificationNotification());
    }

    public function sendPasswordResetOtp(): void
    {
        $this->generateOtpFor(Otp::PASSWORD_RESET);
        $this->notify(new ForgotPasswordNotification());
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(config('tenancy.tenant_model'), BelongsToTenant::$tenantIdColumn);
    }

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_user', 'user_id', 'tenant_id')->withPivot(['status', 'role']);
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn() => trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? '')) ?: null,
        );
    }

    public function otp(): HasMany
    {
        return $this->hasMany(OtpCode::class);
    }

    public function profilePicture(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getFirstMediaUrl('profile_picture'),
        );
    }

    public function farms(): BelongsToMany
    {
        return $this->belongsToMany(Farm::class, 'farm_user', 'user_id', 'farm_id')->withPivot(['status', 'role', 'data']);
    }


    public function isFarmOwner(Farm $farm): bool
    {
        return $this->farms()->where('farm_id', $farm->id)->where('role', Role::FARM_TEAM_OWNER->value)->exists();
    }

    public function loginLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LoginLog::class, 'user_id');
    }
}
