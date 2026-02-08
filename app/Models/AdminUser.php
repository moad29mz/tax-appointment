<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminUser extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'phone',
        'department',
        'permissions',
        'last_login_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'permissions' => 'array',
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // دالة للتحقق من الصلاحيات
    public function hasPermission($permission)
    {
        if ($this->role === 'admin') {
            return true;
        }

        if (!$this->permissions) {
            return false;
        }

        return in_array('all', $this->permissions) || in_array($permission, $this->permissions);
    }

    // دالة للحصول على الأدوار
    public static function getRoles()
    {
        return [
            'admin' => 'مدير النظام',
            'manager' => 'مدير قسم',
            'employee' => 'موظف'
        ];
    }

    // دالة للحصول على الأقسام
    public static function getDepartments()
    {
        return [
            'الإدارة العامة' => 'الإدارة العامة',
            'إدارة المواعيد' => 'إدارة المواعيد',
            'الاستقبال' => 'الاستقبال',
            'المالية' => 'المالية',
            'التقنية' => 'التقنية',
            'الدعم الفني' => 'الدعم الفني'
        ];
    }

    // دالة للحصول على الصلاحيات
    public static function getPermissions()
    {
        return [
            'users' => [
                'users.view' => 'عرض المستخدمين',
                'users.create' => 'إنشاء مستخدمين',
                'users.edit' => 'تعديل المستخدمين',
                'users.delete' => 'حذف المستخدمين'
            ],
            'appointments' => [
                'appointments.view' => 'عرض المواعيد',
                'appointments.create' => 'إنشاء مواعيد',
                'appointments.edit' => 'تعديل المواعيد',
                'appointments.delete' => 'حذف المواعيد'
            ],
            'reports' => [
                'reports.view' => 'عرض التقارير',
                'reports.export' => 'تصدير التقارير'
            ],
            'settings' => [
                'settings.view' => 'عرض الإعدادات',
                'settings.edit' => 'تعديل الإعدادات'
            ]
        ];
    }
}