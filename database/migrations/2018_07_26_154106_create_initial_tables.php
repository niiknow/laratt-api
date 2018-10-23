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
            $table->increments('id');
            $table->uuid('uid')->unique();
            $table->string('email')->unique();
            $table->string('password')->nullable();

            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('photo_url')->nullable();
            $table->string('phone_country_code', 10)->default('1');
            $table->string('phone', 50)->nullable();
            $table->enum('tfa_type', ['off', 'email', 'sms', 'call', 'google_soft_token', 'authy_soft_token', 'authy_onetouch'])->default('off');
            $table->string('authy_id')->unique()->nullable();
            $table->string('authy_status')->nullable();
            $table->string('google_tfa_secret')->nullable();
            $table->string('tfa_code')->nullable();
            $table->timestamp('tfa_exp_at')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('password_updated_at')->nullable();

            // member, admin, etc...
            $table->string('group', 20)->default('member');

            $table->string('email_alt')->nullable();
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('postal', 50)->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->double('lat', 11, 8);
            $table->double('lng', 11, 8);

            // subscription info
            $table->timestamp('email_list_optin_at')->nullable();
            $table->boolean('is_retired_or_unemployed')->default(0);
            $table->string('occupation')->nullable();
            $table->string('employer')->nullable();

            $table->string('stripe_customer_id')->nullable();
            $table->string('card_brand', 50)->nullable();
            $table->string('card_last4', 4)->nullable();

            $table->text('meta1')->nullable();
            $table->text('meta2')->nullable();

            $table->timestamps();
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
    }
}
