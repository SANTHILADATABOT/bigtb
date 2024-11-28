<?php

namespace WeDevs\PM\Monthlycalender\Transformers;

use League\Fractal\TransformerAbstract;
use Carbon\Carbon;

class Overview_Graph_Transformer extends TransformerAbstract {
    public function transform( $item ) {
        return [
            'date_time'  => $item['date_time'],
            'tasks'      => $item['tasks'],
            'activities' => $item['activities'],
        ];
    }
}
