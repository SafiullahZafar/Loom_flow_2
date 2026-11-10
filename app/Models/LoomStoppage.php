<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class LoomStoppage extends Model
{
    protected $fillable = ['loom_no','work_order_id','stopped_on','downtime_minutes','reason','repair_cost','notes'];
    public function order(): BelongsTo { return $this->belongsTo(WorkOrder::class, 'work_order_id'); }
}
