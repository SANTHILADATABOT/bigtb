<?php

namespace WeDevs\PM\Reports\Transformers;

use League\Fractal\TransformerAbstract;
use WeDevs\PM\Common\Traits\Resource_Editors;
use WeDevs\PM\Reports\Models\ReportData;

class ReportDataTransformer extends TransformerAbstract {

    use Resource_Editors;

    public function transform( ReportData $item ) {
        return [
            'id'          => $item->id,
            'report_name' => $item->report_name,
            'title'       => $item->title,
            'col_settings' => $item->columnDataTypes->isEmpty() ?
                [] :
                $item->columnDataTypes->map(function($col) {
                return [
                    'id' => $col->id,
                    'name' => $col->column_name,
                    'datatype' => $col->datatype,
                    'order' => $col->column_order,
                    'report_id' => $col->report_id,
                ];
            })
        ];
    }
}
