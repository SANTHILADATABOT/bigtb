<?php

namespace WeDevs\PM\Monthlycalender\Controllers;

// use WP_REST_Request;
// use WeDevs\PM\Monthlycalender\Models\Calender;
// use League\Fractal;
// use League\Fractal\Resource\Item as Item;
// use League\Fractal\Resource\Collection as Collection;
// use League\Fractal\Pagination\IlluminatePaginatorAdapter;
// use WeDevs\PM\Common\Traits\Transformer_Manager;
// use WeDevs\PM\User\Models\User;
// use WeDevs\PM\User\Models\User_Role;
// use WeDevs\PM\Monthlycalender\Helper\MonthlyCalender_Role_Relation;
// namespace WeDevs\PM\Project\Controllers;

use WP_REST_Request;
use WeDevs\PM\Monthlycalender\Models\Calender;
use League\Fractal;
use League\Fractal\Resource\Item as Item;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use WeDevs\PM\Common\Traits\Transformer_Manager;
use WeDevs\PM\Project\Transformers\Project_Transformer;
use WeDevs\PM\Common\Traits\Request_Filter;
use WeDevs\PM\User\Models\User;
use WeDevs\PM\User\Models\User_Role;
use WeDevs\PM\Category\Models\Category;
use WeDevs\PM\Common\Traits\File_Attachment;
use Illuminate\Pagination\Paginator;
use WeDevs\PM\Common\Models\Meta;
use WeDevs\PM\Task_List\Models\Task_List;
use WeDevs\PM\Project\Helper\Project_Role_Relation;

class Calenders_Controller {

	use Transformer_Manager, File_Attachment;
	public function index( WP_REST_Request $request ) {
		date_default_timezone_set('Asia/Kolkata');
		global $wpdb;
		$table_name = $wpdb->prefix . 'pm_calender';
		$current_user = wp_get_current_user(); // Get the current user object
		$roles = $current_user->user_nicename; // Retrieve an array of roles
		$query = $wpdb->prepare("SELECT * FROM $table_name WHERE delete_status = 'no' and  user_id = %d", $current_user->ID);
		$getDatas = $wpdb->get_results($query);
        $result = [];
		if ($getDatas) {
			foreach ($getDatas as $row) {
				$allDay = $row->allDay === 'Yes' ? true : false;
				$data =[
					'Id' => $row->id ?? null,
					'Subject' => $row->title ?? 'No Title',
					'StartTime' => !empty($row->start_date) ? date('Y-m-d\TH:i:s', strtotime($row->start_date)) : null,
    				'EndTime' => !empty($row->end_date) ? date('Y-m-d\TH:i:s', strtotime($row->end_date)) : null,
					'IsAllDay' => $allDay ?? false,
					'Description' => $row->description ?? 'No Description',
					'RecurrenceRule' => $row->RecurrenceRule ?? null,
					'Location' => $row->location ?? null,
					'DBID' => $row->id,
					'RecurrenceID' =>$row->RecurrenceId ?? null,

				];
				$result[] = $data;
			}
		} else {
			echo "No events found for the given date.";
		}
		$jsonData = json_encode($result);
			return  $jsonData ;
	}

	public function store( WP_REST_Request $request ) {
		// dd('hi');
		global $wpdb;
		$current_user = wp_get_current_user(); // Get the current user object
		$roles = $current_user->user_nicename; // Retrieve an array of roles
		try {
		$start_date_raw = $request['start_date'];
        $start_date_raw = preg_replace('/\sGMT.*$/', '', $start_date_raw);
        $timestampstart = strtotime($start_date_raw);
        $start_date = date('Y-m-d H:i:s', $timestampstart);

        // Parse and format the end_date
        $end_date_raw = $request['end_date'];
        $end_date_raw = preg_replace('/\sGMT.*$/', '', $end_date_raw);
        $timestampend = strtotime($end_date_raw);
        $end_date = date('Y-m-d H:i:s', $timestampend);

        // Parse and format the created_at date
        $todayDate = $request['created_at'];
        $today_date_raw = preg_replace('/\sGMT.*$/', '', $todayDate);
        $timestampToday = strtotime($today_date_raw);
        $created_at = date('Y-m-d H:i:s', $timestampToday);
        $allDay = $request['allDay']===true ? 'Yes' : 'No';
        print_r($created_at);
        // Create the calendar entry
		$data = [
			'title' => $request['title'] ?? '',
            'description' => $request['description'] ?? '',
            'location' => $request['Location'] ?? '',
            'start_date' => $start_date,
            'end_date' => $end_date,
            'resources' => $request['resources'] ?? '',
            'RecurrenceRule' => $request['RecurrenceRule'] ?? '',
            'created_by' => $current_user->ID,
            'user_role' => $roles,
            'user_id' => $current_user->ID,
            'allDay' => $allDay,
			'created_at' => $created_at,
			'RecurrenceId' => $request['RecurrenceId'] ?? null,
		];
		$table_name = $wpdb->prefix . 'pm_calender'; // Adjust the table name if necessary
		$wpdb->insert(
			$table_name, 
			$data
		);
		$query = $wpdb->prepare("SELECT * FROM $table_name WHERE delete_status = 'no' and  user_id = %d", $current_user->ID );
		// Execute the query and get results
		$results = $wpdb->get_results($query);
		return [
			'message' => 'Calendar entry Created successfully.',
			'data'    => $results,
		];
    } catch (\Exception $e) {
		print_r($e->getMessage());
        return new WP_Error('calender_creation_failed', $e->getMessage(), ['status' => 500]);
    }
        // return $data;
	}
	public function update( WP_REST_Request $request ) {
		
		// dd('hi');
		global $wpdb;
		$current_user = wp_get_current_user(); // Get the current user object
		$roles = $current_user->user_nicename; // Retrieve an array of roles
		try {
		$start_date_raw = $request['start_date'];
        $start_date_raw = preg_replace('/\sGMT.*$/', '', $start_date_raw);
        $timestampstart = strtotime($start_date_raw);
        $start_date = date('Y-m-d H:i:s', $timestampstart);

        // Parse and format the end_date
        $end_date_raw = $request['end_date'];
        $end_date_raw = preg_replace('/\sGMT.*$/', '', $end_date_raw);
        $timestampend = strtotime($end_date_raw);
        $end_date = date('Y-m-d H:i:s', $timestampend);

        // Parse and format the created_at date
        $todayDate = $request['updated_at'];
        $today_date_raw = preg_replace('/\sGMT.*$/', '', $todayDate);
        $timestampToday = strtotime($today_date_raw);
        $updated_at = date('Y-m-d H:i:s', $timestampToday);
        $allDay = $request['allDay']===true ? 'Yes' : 'No';
        // print_r($updated_at);
        // Create the calendar entry
		$data = [
			'title' => $request['title'] ?? '',
            'description' => $request['description'] ?? '',
            'location' => $request['Location'] ?? '',
            'start_date' => $start_date,
            'end_date' => $end_date,
            'resources' => $request['resources'] ?? '',
            'RecurrenceRule' => $request['RecurrenceRule'] ?? '',
            'created_by' => $current_user->ID,
            'user_role' => $roles,
            'user_id' => $current_user->ID,
            'allDay' => $allDay,
			'updated_at' => $updated_at,
			'RecurrenceId' => $request['RecurrenceId'] ?? null,
		];
		$table_name = $wpdb->prefix . 'pm_calender'; // Adjust the table name if necessary
		if (!empty($request['id']) && empty($request['RecurrenceId'])) {
            // Update the existing entry
            $update_result = $wpdb->update(
                $table_name,
                $data,
                ['id' => $request['id']] // Where condition
            );

            if ($update_result === false) {
                throw new \Exception('Failed to update the calendar entry.');
            }

            return [
                'message' => 'Calendar entry updated successfully.',
                'data'    => $wpdb->get_row($wpdb->prepare(
					"SELECT * FROM $table_name WHERE delete_status = 'no' AND id = %d ", 
					$request['id']
				)),
            ];
        }
		else{
			$getWhere = $wpdb->get_row($wpdb->prepare(
				"SELECT * FROM $table_name WHERE delete_status = 'no' AND  id = %d ", 
				$request['RecurrenceId']
			));
			$start_date_raw = $request['start_date'];
			$start_date_raw = preg_replace('/\sGMT.*$/', '', $start_date_raw);
			$timestampstart = strtotime($start_date_raw);
			if ($timestampstart !== false) {
				$formattedDate = gmdate("Ymd\THis\Z", $timestampstart);
				print_r($formattedDate);
			} else {
				error_log("Invalid date string: " . $start_date_raw);
				$formattedDate = null;
			}
			$recurrenceRule = $getWhere->RecurrenceRule ?? null; // Extract the current RecurrenceRule
			if ($recurrenceRule && strpos($recurrenceRule, 'EXDATE=') !== false) {
				// Append to existing EXDATE
				$recurrenceRule = preg_replace_callback(
					'/EXDATE=([^;]*)/',
					function ($matches) use ($formattedDate) {
						return $matches[0] . ',' . $formattedDate; // Add new date to EXDATE
					},
					$recurrenceRule
				);
			} else if ($recurrenceRule) {
				// Add EXDATE as a new parameter
				$recurrenceRule .= "EXDATE=" . $formattedDate;
			}
			$update_result = $wpdb->update(
				$table_name,
				['RecurrenceRule' => $recurrenceRule], // Updated RecurrenceRule
				['id' => $request['RecurrenceId']] // Where condition
			);
			// Optionally insert new data if required
			$Insertdata  = [];
			if (!empty($data)) {
				$Insertdata = [
					'title' => $request['title'] ?? '',
					'description' => $request['description'] ?? '',
					'location' => $request['Location'] ?? '',
					'start_date' => $start_date,
					'end_date' => $end_date,
					'resources' => $request['resources'] ?? '',
					'RecurrenceRule' => null,
					'created_by' => $current_user->ID,
					'user_role' => $roles,
					'user_id' => $current_user->ID,
					'allDay' => $allDay,
					'RecurrenceId' => $request['RecurrenceId'],
					'created_at' => $updated_at,
				];
				if(!empty($request['DBID']) && $request['DBID'] == $request['id'])
				{
					$UpdateWhere = $wpdb->get_row($wpdb->prepare(
						"SELECT * FROM $table_name WHERE delete_status = 'no' AND id = %d ", 
						$request['DBID']
					));
					if($UpdateWhere)
					{
						$update_result = $wpdb->update(
							$table_name,
							$data,
							['id' => $request['DBID']] // Where condition
						);
					}
					else{
						$wpdb->insert($table_name, $Insertdata);
					}
				}
				else{
					$wpdb->insert($table_name, $Insertdata);
				}
			}
		}
		$query = $wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d", $current_user->ID);
		// Execute the query and get results
		$results = $wpdb->get_results($query);
		//  return $results;
		return [
			'message' => 'Calendar entry updated successfully.',
			'data'    => $results,
		];
    } catch (\Exception $e) {
		print_r($e->getMessage());
        return new WP_Error('calender_creation_failed', $e->getMessage(), ['status' => 500]);
    }
}
	public function destroy( WP_REST_Request $request ) {
		global $wpdb;
		$data = [
			'title' => $request['title'] ?? '',
            'description' => $request['description'] ?? '',
            'location' => $request['Location'] ?? '',
            'start_date' => $start_date,
            'end_date' => $end_date,
            'resources' => $request['resources'] ?? '',
            'RecurrenceRule' => $request['RecurrenceRule'] ?? '',
            'created_by' => $current_user->ID,
            'user_role' => $roles,
            'user_id' => $current_user->ID,
            'allDay' => $allDay,
			'updated_at' => $updated_at,
			'RecurrenceId' => $request['RecurrenceId'] ?? null,
		];
		$table_name = $wpdb->prefix . 'pm_calender'; // Adjust the table name if necessary
		if (!empty($request['id']) && empty($request['RecurrenceId']))
		{
			$delete_result = $wpdb->update(
                $table_name,
				['delete_status' => 'yes'],
                ['id' => $request['id']] // Where condition
            );
            return [
                'message' => 'Calendar entry Deleted successfully.',
                'data'    => $wpdb->get_row($wpdb->prepare(
					"SELECT * FROM $table_name WHERE  delete_status = 'no' AND id = %d", 
					$request['id']
				)),
            ];
		}
		else{
			// Optionally insert new data if required
			if (!empty($data)) {
				if(!empty($request['DBID']) && $request['DBID'] == $request['id'])
				{
					$updateOtherData =  $wpdb->get_row($wpdb->prepare(
						"SELECT * FROM $table_name WHERE RecurrenceId = %d ", 
						$request['DBID']
					));
					if($updateOtherData)
					{
						$update_result = $wpdb->update(
							$table_name,
							['RecurrenceId' => 'null'],
							['id' => $updateOtherData->id] ,// Where condition
						);
					}
					$UpdateWhere = $wpdb->get_row($wpdb->prepare(
						"SELECT * FROM $table_name WHERE delete_status = 'no' AND id = %d ", 
						$request['DBID']
					));
					if($UpdateWhere)
					{
						$update_result = $wpdb->update(
							$table_name,
							['delete_status' => 'yes'],
							['id' => $request['DBID']] ,// Where condition
						);
					}
				}
			}
		}
		// $query = $wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d", $current_user->ID );
		$query = $wpdb->prepare("SELECT * FROM $table_name WHERE delete_status = 'no' and user_id = %d", $current_user->ID  );
		// Execute the query and get results
		$results = $wpdb->get_results($query);
		//  return $results;
		return [
			'message' => 'Calendar entry updated successfully.',
			'data'    => $results,
		];
		
		}
	


}
