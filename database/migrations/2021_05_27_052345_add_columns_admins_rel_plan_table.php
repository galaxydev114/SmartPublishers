<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsAdminsRelPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admins', function (Blueprint $table) {
            if (!Schema::hasColumn('admins', 'is_subscribed'))
                $table->tinyInteger('is_subscribed')->nullable()->default(0)->comment('Subscribed Status')->after('is_super');
            if (!Schema::hasColumn('admins', 'subscribe_id'))
                $table->integer('subscribe_id')->nullable()->default(0)->comment('Subscribe ID')->after('is_subscribed');
            if (!Schema::hasColumn('admins', 'plan_id'))
                $table->string('plan_id')->nullable()->default('')->comment('Plan ID')->after('subscribe_id');
            if (!Schema::hasColumn('admins', 'agreement_id'))
                $table->string('agreement_id')->nullable()->default('')->comment('Agreement ID')->after('plan_id');
            if (!Schema::hasColumn('admins', 'trial_type'))
                $table->tinyInteger('trial_type')->nullable()->default(0)->comment('Trial Type')->after('agreement_id');
            if (!Schema::hasColumn('admins', 'trial_start'))
                $table->string('trial_start')->nullable()->default('')->comment('Trial Start')->after('trial_type');
            if (!Schema::hasColumn('admins', 'trial_end'))
                $table->string('trial_end')->nullable()->default('')->comment('Trial End')->after('trial_start');
            if (!Schema::hasColumn('admins', 'stripe_id'))
                $table->string('stripe_id')->nullable()->default('')->comment('Stripe ID')->after('trial_end');
            if (!Schema::hasColumn('admins', 'card_brand'))
                $table->string('card_brand')->nullable()->default('')->comment('Card Brand')->after('stripe_id');
            if (!Schema::hasColumn('admins', 'card_last_four'))
                $table->string('card_last_four', 4)->nullable()->default('')->comment('Card Last Four')->after('card_brand');
            if (!Schema::hasColumn('admins', 'trial_ends_at'))
                $table->timestamp('trial_ends_at')->nullable()->after('card_last_four');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('is_implant_system4');
            $table->dropColumn('is_size4');
            $table->dropColumn('is_implant_system5');
            $table->dropColumn('is_size5');
        });
    }
}
