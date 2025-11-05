<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements MustVerifyEmail, JWTSubject
{
    protected $table = 'nguoi_dung';
    protected $primaryKey = 'id_nguoi_dung';
    public $incrementing = true;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Lấy định danh (identifier) sẽ được lưu trong 'sub' claim của JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Trả về một mảng key/value chứa bất kỳ custom claims nào
     * bạn muốn thêm vào JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'name'=> $this->name,
            'email'=> $this->email,
            'sdt'=> $this->sdt,
            'ngay_sinh'=>$this->ngay_sinh
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'sdt',
        'ngay_sinh',
        'email_verified_at',
        'quyen'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */

    // Trong Laravel, khi bạn trả về model dưới dạng JSON response (ví dụ API), thì các field trong $hidden sẽ bị ẩn đi, không hiển thị.
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
}
