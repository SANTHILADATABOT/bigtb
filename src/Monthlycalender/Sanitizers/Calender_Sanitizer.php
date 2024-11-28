<?php

namespace WeDevs\PM\Monthlycalender\Sanitizers;

use WeDevs\PM\Core\Sanitizer\Abstract_Sanitizer;

class Calender_Sanitizer extends Abstract_Sanitizer {
	public function filters() {
        return [
            'projectable_type' => 'trimer',
            'title'            => 'trimer|sanitize_text_field',
            'description'      => 'trimer|sanitize_text_field',
            'status'           => 'trimer',
        ];
    }
}
