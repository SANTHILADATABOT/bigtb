<?php
namespace WeDevs\PM\Materials\Models;

use WeDevs\PM\Core\DB_Connection\Model as Eloquent;
use WeDevs\PM\Common\Traits\Model_Events;
use Carbon\Carbon;

class MaterialOrder extends Eloquent {

    use Model_Events;
    public $timestamps = false;
    protected $table = 'pm_material_orders';
    protected $fillable = [
        'id',
        'vendor_id',
        'cost',
        'title',
        'description',
        'date',
        'ordered_by'
    ];

    public function vendor() {
        return $this->belongsTo('WeDevs\PM\Materials\Models\MaterialVendor', 'vendor_id');
    }

    public function projects() {
        return $this->belongsToMany('WeDevs\PM\Project\Models\Project',
        pm_tb_prefix() . 'pm_material_orders_projects', 'order_id', 'project_id');
    }

    public function orderedBy() {
        return $this->belongsTo('App\User', pm_tb_prefix() . 'users', 'ordered_by');
    }
}
