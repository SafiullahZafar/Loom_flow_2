<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales_contracts', function (Blueprint $table) {
            $table->string('party_contract_no', 80)->nullable();
            $table->string('contract_type', 30)->default('Conversion');
            $table->string('loom_type', 40)->nullable();
            $table->string('buyer_code', 40)->nullable();
            $table->string('buyer_gst', 60)->nullable();
            $table->string('broker_code', 40)->nullable();
            $table->string('broker_name', 120)->nullable();
            $table->decimal('broker_commission_per_meter', 14, 4)->default(0);
            $table->string('quality_code', 40)->nullable();
            $table->string('contract_quality', 120)->nullable();
            $table->decimal('contract_quality_width', 12, 3)->nullable();
            $table->decimal('panna', 12, 3)->default(1);
            $table->decimal('kp_percentage', 7, 3)->default(0);
            $table->unsignedSmallInteger('kp_days')->default(0);
            $table->decimal('pp_percentage', 7, 3)->default(0);
            $table->unsignedSmallInteger('pp_days')->default(0);
            $table->decimal('read', 12, 3)->nullable();
            $table->decimal('pick', 12, 3)->nullable();
            $table->decimal('warp_count', 12, 4)->nullable();
            $table->decimal('weft_count', 12, 4)->nullable();
            $table->decimal('width', 12, 3)->nullable();
            $table->decimal('yarn_warp_rate', 14, 4)->nullable();
            $table->decimal('yarn_weft_rate', 14, 4)->nullable();
            $table->decimal('conversion_rate', 14, 4)->nullable();
            $table->decimal('warp_wt_40m', 16, 6)->default(0);
            $table->decimal('weft_wt_40m', 16, 6)->default(0);
            $table->decimal('warp_weight_1m', 16, 6)->default(0);
            $table->decimal('weft_weight_1m', 16, 6)->default(0);
            $table->decimal('total_weight_1m_lb', 16, 6)->default(0);
            $table->decimal('total_weight_1m_kg', 16, 6)->default(0);
            $table->decimal('width_m', 12, 6)->default(0);
            $table->decimal('gsm', 12, 2)->default(0);
            $table->decimal('warp_bags', 16, 4)->default(0);
            $table->decimal('weft_bags', 16, 4)->default(0);
            $table->decimal('warp_amount_per_mtr', 14, 4)->default(0);
            $table->decimal('weft_amount_per_mtr', 14, 4)->default(0);
            $table->decimal('conversion_per_mtr', 14, 4)->default(0);
            $table->decimal('yarn_value', 14, 2)->default(0);
            $table->decimal('conv_value', 14, 2)->default(0);
            $table->decimal('sale_tax_rate', 6, 4)->default(0);
            $table->decimal('sales_tax_amount', 14, 2)->default(0);
            $table->text('delivery_instructions')->nullable();
            $table->text('payment_instructions')->nullable();
            $table->text('quality_instructions')->nullable();
            $table->text('other_instructions')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_contracts', function (Blueprint $table) {
            $table->dropColumn([
                'party_contract_no', 'contract_type', 'loom_type', 'buyer_code', 'buyer_gst', 'broker_code',
                'broker_name', 'broker_commission_per_meter', 'quality_code', 'contract_quality',
                'contract_quality_width', 'panna', 'kp_percentage', 'kp_days', 'pp_percentage', 'pp_days',
                'read', 'pick', 'warp_count', 'weft_count', 'width', 'yarn_warp_rate', 'yarn_weft_rate',
                'conversion_rate', 'warp_wt_40m', 'weft_wt_40m', 'warp_weight_1m', 'weft_weight_1m',
                'total_weight_1m_lb', 'total_weight_1m_kg', 'width_m', 'gsm', 'warp_bags', 'weft_bags',
                'warp_amount_per_mtr', 'weft_amount_per_mtr', 'conversion_per_mtr', 'yarn_value',
                'conv_value', 'sale_tax_rate', 'sales_tax_amount', 'delivery_instructions',
                'payment_instructions', 'quality_instructions', 'other_instructions',
            ]);
        });
    }
};
