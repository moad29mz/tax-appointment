<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'type', 'group'];

    // دالة للحصول على قيمة الإعداد
    public static function getValue($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    // دالة لتعيين قيمة الإعداد
    public static function setValue($key, $value)
    {
        $setting = self::where('key', $key)->first();
        
        if ($setting) {
            $setting->update(['value' => $value]);
        } else {
            self::create([
                'key' => $key,
                'value' => $value,
                'type' => 'text',
                'group' => 'general'
            ]);
        }
    }

    // دالة للحصول على جميع الإعدادات مصنفة حسب المجموعة
    public static function getAllGrouped()
    {
        $settings = self::all();
        $grouped = [];
        
        foreach ($settings as $setting) {
            $grouped[$setting->group][$setting->key] = [
                'value' => $setting->value,
                'type' => $setting->type
            ];
        }
        
        return $grouped;
    }
}