<?php

namespace WeDevs\PM\Reports\Transformers;

use League\Fractal\TransformerAbstract;
use WeDevs\PM\User\Transformers\User_Transformer;
use WeDevs\PM\Common\Traits\Resource_Editors;
use Carbon\Carbon;
use WeDevs\PM\Task\Models\Task;
use WeDevs\PM\Global_Kanboard\Models\Global_Kanboard;
use WeDevs\PM\Task\Transformers\Task_Transformer;

class ReportTransformer extends TransformerAbstract {

    use Resource_Editors;

    public function transform($item) {
        $vars = get_object_vars($item);
        foreach ($vars as $key => $value) {
            if (is_numeric($value) && $key !== 'record_number' && $key !== 'status' && $key !== 'part-number') {
                $vars[$key] = number_format($value, 2);
            }
        }
        return $vars;
    }
}
