<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInitialTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->nullable();
            $table->string('email')->unique();

            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('password')->nullable();
            $table->string('photo_url')->nullable();

            $table->string('phone', 50)->nullable();
            $table->enum('tfa_type', ['off', 'email', 'sms', 'call', 'google_soft_token', 'authy_soft_token', 'authy_onetouch'])->default('off');
            $table->string('authy_id')->unique()->nullable();
            $table->string('authy_status')->nullable();
            $table->string('google_tfa_secret')->nullable();
            $table->string('tfa_code')->nullable();
            $table->timestamp('tfa_exp_at')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('password_updated_at')->nullable();

            // admin, partner, member, revoked
            $table->string('access', 20)->default('member');

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user');
        Schema::dropIfExists('password_reset');
    }
}
