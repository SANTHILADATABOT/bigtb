<?php

namespace WeDevs\PM\Reports\Models;

use WeDevs\PM\Core\DB_Connection\Model as Eloquent;
use WeDevs\PM\Reports\Models\ColumnDataType;

class ReportData extends Eloquent
{
    protected $table = 'pm_report_data';
    public $timestamps = false;
    protected $fillable = ['report_name', 'title'];

    public function columnDataTypes(): object
    {
        return $this->hasMany(ColumnDataType::class, 'report_id');
    }
}
