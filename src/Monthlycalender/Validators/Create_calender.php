<?php

namespace WeDevs\PM\Monthlycalender\Validators;

use WeDevs\PM\Core\Validator\Abstract_Validator;

class Create_calender extends Abstract_Validator {
    public function messages() {
        return [
            'title.required' => __( 'Calender title is required.', 'wedevs-project-manager' ),
            'title.pm_unique' => __( 'Calender title must be unique.', 'wedevs-project-manager' ),
        ];
    }

    public function rules() {
        
        if ( apply_filters( 'pm_check_calender_title_unique', true ) ) {
            return [
                'title'  => 'required|pm_unique:calender,title',
            ];
        } 
        
        return [
            'title'  => 'required',
        ];
    }
}
