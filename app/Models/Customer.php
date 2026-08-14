<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_code',
        'name',
        'phone',
        'email',
        'address',
        'city',
        'latitude',
        'longitude',
        'notes',
    ];

    protected static function booted(): void
    {
        static::creating(function (Customer $customer) {
            if (empty($customer->customer_code)) {
                $customer->customer_code = static::generateCode();
            }
        });
    }

    public static function generateCode(): string
    {
        do {
            $code = 'CUST-'.date('Y').'-'.str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (static::where('customer_code', $code)->exists());

        return $code;
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
