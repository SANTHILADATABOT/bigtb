<?php

namespace WeDevs\PM\Reports\Transformers;

use League\Fractal\TransformerAbstract;
use WeDevs\PM\Common\Traits\Resource_Editors;
use WeDevs\PM\Reports\Models\ReportData;

class EmployeeTransformer extends TransformerAbstract
{
    use Resource_Editors;

    public function transform( $item ) {
        return [
            'id'   => $item->recnum,
            'name' => $item->fstnme . ' ' . $item->lstnme,
            'abb'  => $item->fstnme . ' ' . $item->lstnme[0],
            'first' => $item->fstnme,
            'last' => $item->lstnme,
        ];
    }
}
