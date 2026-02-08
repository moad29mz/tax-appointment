<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('admin_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'manager', 'employee'])->default('employee');
            $table->boolean('is_active')->default(true);
            $table->string('phone')->nullable();
            $table->string('department')->nullable();
            $table->text('permissions')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        // إضافة المستخدمين الأساسيين
        DB::table('admin_users')->insert([
            [
                'name' => 'المسؤول الرئيسي',
                'email' => 'admin@tax.gov',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
                'is_active' => true,
                'phone' => '+212600000001',
                'department' => 'الإدارة العامة',
                'permissions' => json_encode(['all']),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'مدير المواعيد',
                'email' => 'manager@tax.gov',
                'password' => bcrypt('manager123'),
                'role' => 'manager',
                'is_active' => true,
                'phone' => '+212600000002',
                'department' => 'إدارة المواعيد',
                'permissions' => json_encode(['appointments.view', 'appointments.edit', 'reports.view']),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'موظف الاستقبال',
                'email' => 'employee@tax.gov',
                'password' => bcrypt('employee123'),
                'role' => 'employee',
                'is_active' => true,
                'phone' => '+212600000003',
                'department' => 'الاستقبال',
                'permissions' => json_encode(['appointments.view']),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('admin_users');
    }
};