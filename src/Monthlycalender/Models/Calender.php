<?php

namespace WeDevs\PM\Monthlycalender\Models;
use WeDevs\PM\Common\Traits\Model_Events;
use WeDevs\PM\Core\DB_Connection\Model as Eloquent;

class Calender extends Eloquent {

	use  Model_Events;
    protected $table = 'pm_calender';
    protected $fillable = [
      'title', 'description', 'location', 'start_date', 'end_date',
      'resources', 'RecurrenceRule', 'created_by', 'user_role', 'user_id', 'allDay'
  ];

}
