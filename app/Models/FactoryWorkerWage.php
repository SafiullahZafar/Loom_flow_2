<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class FactoryWorkerWage extends Model
{
    protected $fillable = ['worker_name','work_order_id','loom_no','wage_date','basis','quantity','rate','gross_amount','advance_adjustment','paid_amount','balance_amount','notes'];
    public function order(): BelongsTo { return $this->belongsTo(WorkOrder::class, 'work_order_id'); }
}
