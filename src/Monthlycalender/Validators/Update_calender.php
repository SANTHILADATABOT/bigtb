<?php

namespace WeDevs\PM\Monthlycalender\Validators;

use WeDevs\PM\Core\Validator\Abstract_Validator;

class Update_calender extends Abstract_Validator {
    public function messages() {
        return [
            'title.required'  => __( 'Calender title is required.', 'wedevs-project-manager' ),
            'title.pm_unique' => __( 'Calender title must be unique.', 'wedevs-project-manager' ),
            'id.required'     => __( 'Calender ID is required.', 'wedevs-project-manager' ),
            'id.gtz'          => __( 'Calender ID must be greater than zero', 'wedevs-project-manager' ),
        ];
    }

    public function rules() {
        $id = $this->request->get_param( 'id' );
        
        if(is_array( $id )) {
            return [];
        }
        
        return [
            'title' => 'required|pm_unique:calender,title,'.$id,
            'id'    => 'required|gtz', //Greater than zero (gtz)
        ];
    }
}
