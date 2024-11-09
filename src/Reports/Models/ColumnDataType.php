<?php

namespace WeDevs\PM\Reports\Models;

use WeDevs\PM\Core\DB_Connection\Model as Eloquent;
use WeDevs\PM\Reports\Models\ReportData;

class ColumnDataType extends Eloquent
{
    protected $table = 'pm_column_datatypes';
    public $timestamps = false;
    protected $fillable = ['column_name', 'datatype', 'report_id'];

    public function reportData(): object
    {
        return $this->belongsTo(ReportData::class, 'report_id');
    }
}
