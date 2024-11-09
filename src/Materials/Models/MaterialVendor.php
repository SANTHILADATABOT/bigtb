<?php
namespace WeDevs\PM\Materials\Models;

use WeDevs\PM\Core\DB_Connection\Model as Eloquent;
use WeDevs\PM\Common\Traits\Model_Events;
use Carbon\Carbon;

class MaterialVendor extends Eloquent {

    use Model_Events;
    public $timestamps = false;
    protected $table = 'pm_material_vendors';
    protected $fillable = [
        'name',
        'description',
        'phone',
        'email',
        'address'
    ];

    public function materialOrders() {
        return $this->hasMany('WeDevs\PM\Materials\Models\MaterialOrder', 'vendor_id');
    }
}
