<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * A message sent from the Contact page form (the visitor then continues on WhatsApp).
 */
class ContactMessage extends Model
{
    protected $fillable = ['first_name', 'last_name', 'email', 'phone', 'message', 'read_at'];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    protected function name(): Attribute
    {
        return Attribute::get(fn () => trim("{$this->first_name} {$this->last_name}"));
    }

    /**
     * wa.me link to reply. Indonesian numbers written with a leading 0 get the 62 country code.
     */
    protected function whatsappUrl(): Attribute
    {
        return Attribute::get(function () {
            $digits = preg_replace('/\D+/', '', (string) $this->phone);
            if (str_starts_with($digits, '0')) {
                $digits = '62' . substr($digits, 1);
            }

            return strlen($digits) >= 8 ? "https://wa.me/{$digits}" : null;
        });
    }
}
