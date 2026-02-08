<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, image, email, phone, address, select
            $table->string('group')->default('general'); // general, appearance, contact, system
            $table->timestamps();
        });

        // إضافة البيانات الأساسية
        DB::table('settings')->insert([
            [
                'key' => 'organization_name',
                'value' => 'المديرية العامة للضرائب',
                'type' => 'text',
                'group' => 'general',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'municipality_name',
                'value' => 'بلدية الرباط',
                'type' => 'text',
                'group' => 'general',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'logo',
                'value' => null,
                'type' => 'image',
                'group' => 'appearance',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'official_email',
                'value' => 'contact@tax.gov',
                'type' => 'email',
                'group' => 'contact',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'phone_number',
                'value' => '+212537200000',
                'type' => 'phone',
                'group' => 'contact',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'address',
                'value' => 'شارع محمد الخامس، الرباط، المغرب',
                'type' => 'text',
                'group' => 'contact',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'language',
                'value' => 'ar',
                'type' => 'select',
                'group' => 'system',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'working_hours_start',
                'value' => '09:00',
                'type' => 'time',
                'group' => 'system',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'working_hours_end',
                'value' => '16:30',
                'type' => 'time',
                'group' => 'system',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
};