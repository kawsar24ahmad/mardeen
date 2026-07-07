<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_name',
        'site_logo',
        'admin_email',
        'site_description',
        'facebook_url',
        'whats_up_number',
        'top_bar_text',
        'twitter_url',
        'maintenance_mode',
    ];

    /**
     * Helper to quickly fetch settings in your blade files/controllers
     */
    public static function getSettings()
    {
        return self::first() ?? new self();
    }
}
