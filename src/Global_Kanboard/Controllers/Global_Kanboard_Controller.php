<?php
namespace WeDevs\PM\Global_Kanboard\Controllers;

use Reflection;
use WP_REST_Request;
use League\Fractal;
use League\Fractal\Resource\Item as Item;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use WeDevs\PM\Common\Traits\Transformer_Manager;
use WeDevs\PM\Project\Models\Project;
use WeDevs\PM\Common\Models\Boardable;
use WeDevs\PM\Common\Traits\Request_Filter;
use Carbon\Carbon;
use WeDevs\PM\Task\Models\Task;
use WeDevs\PM\Global_Kanboard\Models\Global_Kanboard;
use WeDevs\PM\Global_Kanboard\Transformers\Global_Kanboard_Transformer;
use WeDevs\PM\Global_Kanboard\Models\Global_Kanboard_Boardable;
use WeDevs\PM\Global_Kanboard\Transformers\Global_Kanboard_Task_Transformer;
use Illuminate\Database\Capsule\Manager as Capsule;
use WeDevs\PM\Task\Transformers\Task_Transformer;
use WeDevs\PM\Project\Transformers\Project_Transformer;
use Illuminate\Pagination\Paginator;
use WeDevs\PM\Task\Controllers\Task_Controller;
use WeDevs\PM\Common\Models\Assignee;
use WeDevs\PM\Task_List\Transformers\List_Task_Transformer;
use WeDevs\PM\task\Helper\Task as Advanced_Task;
use WeDevs\PM\Task\Helper\Task as Task_Helper;

use WeDevs\PM\Project\Helper\Project as Project_Helper;

class Global_Kanboard_Controller {

    use Transformer_Manager, Request_Filter;

    private static $instance;
    private static $global_kanboard_id = 999999;

    public static function getInstance() {
        if ( !self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function index( WP_REST_Request $request ) {
    // this is for the global kanboard, the table pm_boards uses project ids
    // however since this doesn't belong to a project, we use the global kanboard id
    // which is 999999 (arbitrary, fixed)

        $boards = Global_Kanboard::with('meta')
            ->where('project_id', self::$global_kanboard_id)
            ->where( 'type', 'kanboard' )
            ->orderBy( 'order', 'ASC' )
            ->get();

        $resource = new Collection( $boards, new Global_Kanboard_Transformer );

        return $this->get_response( $resource );
    }

    public function store_searchable_task(WP_REST_Request $request) {
        $board_id   = $request->get_param( 'board_id' );
        $project_id = $request->get_param( 'project_id' );
        $task_id    = $request->get_param( 'task_id' );

        $this->store_column_task( $board_id, $project_id, $task_id );

        wp_send_json_success();
    }

    public function store_column_task( $board_id, $project_id, $task_id ) {
        //where('board_id', $board_id)
        $has_task = Boardable::where('board_type', 'kanboard')
            ->where('boardable_id', $task_id)
            ->first();

        if ( ! $has_task ) {

            $data = [
                'board_id'       => $board_id,
                'board_type'     => 'kanboard',
                'boardable_id'   => $task_id,
                'boardable_type' => 'task',
                'order'          => 0
            ];

            Boardable::create($data);

        }

        return [];
    }

    public function store_searchable_project(WP_REST_Request $request) {

        $board_id   = $request->get_param( 'board_id' );
        $project_id = $request->get_param( 'project_id' );

        $has_project = Boardable::where('board_type', 'kanboard')
            ->where('boardable_id', $project_id)
            ->first();

        if ( ! $has_project ) {

            $data = [
                'board_id'       => $board_id,
                'board_type'     => 'kanboard',
                'boardable_id'   => $project_id,
                'boardable_type' => 'project',
                'order'          => 0
            ];

            Boardable::create($data);

        }

        return [];
    }

    public function hasDefaultBoard( $project_id ) {
        $default_board = Global_Kanboard::where( 'project_id', $project_id )
            ->where( 'type', 'kanboard' )
            ->get()
            ->toArray();

        if ( ! $default_board ) {
            return false;
        }

        return true;
    }

    protected function setDefaultBoard() {
    // Creates the default board for a new kanban instance
    // This instance then becomes the "default board" even if the instance is edited
        self::$global_kanboard_id;

        $default = array(
            array(
                'title'      => __( 'Sales Call', 'kbc' ),
                'order'      => 0,
                'type'       => 'kanboard',
                'project_id' => self::$global_kanboard_id
            ),
            array(
                'title'       => __( 'Estimate Delivered', 'kbc' ),
                'order'      => 1,
                'type'       => 'kanboard',
                'project_id' => self::$global_kanboard_id
            ),
            array(
                'title'       => __( 'Job in Progress', 'kbc' ),
                'order'      => 2,
                'type'       => 'kanboard',
                'project_id' => self::$global_kanboard_id
            ),
            array(
                'title'       => __( 'Paid', 'kbc' ),
                'order'      => 3,
                'type'       => 'kanboard',
                'project_id' => self::$global_kanboard_id
            ),

            array(

                'title'      => __( 'Completed', 'kbc' ),
                'order'      => 4,
                'type'       => 'kanboard',
                'project_id' => self::$global_kanboard_id
            )
        );

        $default = apply_filters( 'pm_kanban_default_boards', $default, self::$global_kanboard_id );

        Global_Kanboard::insert( $default );
    }

    public function show( WP_REST_Request $request ) {
    // this is the function to return tasks for the entire Global Kanban)
        $board_id        = $request->get_param( 'board_id' );
        $per_page        = 50;
        $boardable_table = 'pm_boardables';
        $project_table   = 'pm_projects';

        $projects = Global_Kanboard::with('projects')
            ->find($board_id)
            ->projects();

        $projects = apply_filters( 'pm_projects_query', $projects,  self::$global_kanboard_id, $request );
        $projects = $projects->paginate( $per_page, ['*'] );

        $project_collection = $projects->getCollection();

        $resource = new Collection( $project_collection, new Project_Transformer );

        return $this->get_response( $resource );
    }


    public function store( WP_REST_Request $request ) {
        // Adds a new board to the global kanban - "boards" are columns in the DB
        $latest_order = Global_Kanboard::latest_order( self::$global_kanboard_id, 'kanboard' );

        $data = [
            'title'      => $request->get_param( 'title' ),
            'order'      => $latest_order + 1,
            'type'       => 'kanboard',
            'project_id' => self::$global_kanboard_id,
        ];

        $kanboard = Global_Kanboard::create($data);

        $resource = new Item( $kanboard, new Global_Kanboard_Transformer );
        $message  = [
            'message' => 'New board has been created successfully'
        ];

        return $this->get_response( $resource, $message );
    }


    public function update( WP_REST_Request $request ) {
        $board_id = $request->get_param( 'board_id' );
        $old_idx  = $request->get_param( 'old_idx' );
        $new_idx  = $request->get_param( 'new_idx' );

        $board_moved = Global_Kanboard::find($board_id );

        if ( ! $board ) {
            return false;
        }

        $data = [
            'order' => $new_idx
        ];

        $board->update_model( $data );

        $resource = new Item( $board, new Global_Kanboard_Transformer );

        $message = [
            'message' => 'Your trcking time was stop successfully'
        ];

        return $this->get_response( $resource, $message );
    }

    public function destroy( WP_REST_Request $request ) {
        $board_id = $request->get_param( 'board_id' );

        $board = Global_Kanboard::where( 'id', $board_id )->first();
        $this->delete_all_relation($board);
        $board->delete();

        $message = [
            'message' => 'Board deleted successfully'
        ];

        return $this->get_response(null, $message);
    }

    public function update_boardable( WP_REST_Request $request ) {
        $from_board_id = $request->get_param( 'from_board_id' );
        $project_id = $request->get_param( 'project_id' );
        $to_board_id = $request->get_param( 'to_board_id' );

        $boardable = Boardable::where( 'board_id', $from_board_id )
            ->where( 'board_type', 'kanboard' )
            ->where( 'boardable_id', $project_id )
            ->first();

        $boardable->update(['board_id' => $to_board_id]);

        $message = [
            'message' => 'Project successfully moved from board $from_board_id to board $to_board_id'
        ];

        return $this->get_response(null, $message);
    }

    public function remove_boardable( WP_REST_Request $request ) {
        $board_id = $request->get_param( 'board_id' );
        $project_id = $request->get_param( 'project_id' );

        // Select the time
        $boardable = Boardable::where( 'board_id', $board_id )
            ->where( 'board_type', 'kanboard' )
            ->where( 'boardable_id', $project_id )
            ->first();

        $boardable->delete();

        $message = [
            'message' => 'Project removed from board successfully'
        ];

        return $this->get_response(null, $message);
    }

    function delete_all_relation(Global_Kanboard $board) {
        $board->boardables()->delete();
    }

    function board_order( WP_REST_Request $request ) {
        $board_orders = $request->get_param('board_orders');

        foreach ( $board_orders as $value) {
            Global_Kanboard::where( 'project_id', self::$global_kanboard_id )
                ->where( 'type', 'kanboard' )
                ->where( 'id', $value['id'] )
                ->update( ['order' => $value['order']] );
        }

        wp_send_json_success();
    }

    function task_order( WP_REST_Request $request ) {

        $is_move         = $request->get_param('is_move');
        $board_id        = $request->get_param('section_id');
        $sender_board_id = $request->get_param('sender_section_id');
        $task_ids        = $request->get_param('task_ids');
        $project_id      = $request->get_param('project_id');
        $task_id         = $request->get_param('dragabel_task_id');

        if ( $is_move == 'yes' ) {
            $from_sender_board = Boardable::where('board_id', $sender_board_id)
                ->where('board_type', 'kanboard')
                ->where('boardable_type', 'task')
                ->whereIn('boardable_id', $task_ids)
                ->get()
                ->toArray();

            foreach ( $from_sender_board as $key => $board ) {
                Boardable::where('id', $board['id'])
                    ->update(['board_id' => $board_id]);
            }

            $this->check_automation( $request->get_params() );
        } else {

            foreach ( $task_ids as $order_key => $task_id ) {

                $boardable = Boardable::where('board_id', $board_id)
                    ->where('board_type', 'kanboard')
                    ->where('boardable_type', 'task')
                    ->where('boardable_id', $task_id)
                    ->first();

                if ( $boardable ) {
                    $boardable->update(['order' => $order_key]);
                }
            }
        }

        wp_send_json_success([
            'drag_task' => Task_Controller::get_task( $task_id, $project_id )
        ]);
    }
    /**
     * When task drop one column to another column for changing their status
     */
    function check_automation( $params ) {
        $project_id = $params['project_id'];
        $board_id   = $params['section_id'];
        $task_id    = $params['dragabel_task_id'];

        $meta = \WeDevs\PM\Common\Models\Meta::where( 'project_id', $project_id )
            ->where( 'entity_type',  'kanboard' )
            ->where( 'meta_key', 'automation' )
            ->where( 'entity_id', $board_id )
            ->first();

        if ( ! $meta ) {
            return;
        }

        $meta_value = $meta['meta_value'];

        if ( $meta['project_id'] != $project_id ) {
            return;
        }

        if ( isset( $params['is_move'] ) && $params['is_move'] == 'yes' ) {
            if ( ! empty( $meta_value['users'] ) ) {
                $users_id = wp_list_pluck( $meta_value['users'], 'id' );

                $prev_users = Assignee::select('assigned_to')
                    ->where('task_id', $task_id)
                    ->where('project_id', $project_id)
                    ->get()
                    ->toArray();

                $prev_users = wp_list_pluck( $prev_users, 'assigned_to' );

                $users_id = array_unique( array_merge( $users_id, $prev_users ) );

                $task = [
                    'task_id'    => $task_id,
                    'assignees'  => $users_id
                ];

                Task_Controller::task_update( $task );
            }

            if ( $meta_value['taskStatus'] == 'completed' ) {

                $task = [
                    'task_id' => $task_id,
                    'status'  => 1
                ];

                Task_Controller::task_update( $task );
            }

            if ( $meta_value['taskStatus'] == 'incomplete' ) {

                $task = [
                    'task_id' => $task_id,
                    'status'  => 0
                ];

                Task_Controller::task_update( $task );
            }
        }
    }

    function delte_task( WP_REST_Request $request ) {
        $board_id = $request->get_param('board_id');
        $task_id = $request->get_param('task_id');

        $boardable = Boardable::where('board_id', $board_id)
            ->where('board_type', 'kanboard')
            ->where('boardable_type', 'task')
            ->where('boardable_id', $task_id)
            ->first();

        if ( $boardable ) {
            $boardable->delete();
        }

        return $this->get_response( null );
    }

    function list_view_type( WP_REST_Request $request ) {
        $user_id = get_current_user_id();
        $view_type = $request->get_param('view_type');
        $project_id = $request->get_param('project_id');

        pm_update_meta( $user_id, $project_id, 'list_view', 'list_view_type', $view_type );

        return $this->get_response(null);
    }

    static function after_new_comment( $response, $params ) {
        return $response;
        if( $params['commentable_type'] == 'task' ) {

            $project_id = empty( $params['project_id'] ) ? 0 : intval( $params['project_id'] );
            $task_id = $params['commentable_id'];

            $metas = \WeDevs\PM\Common\Models\Meta::where( 'project_id', $project_id )
                ->where( 'entity_type',  'kanboard' )
                ->where( 'meta_key', 'automation' )
                ->get()
                ->toArray();

            foreach ( $metas as $key => $meta ) {
                $meta_value = $meta['meta_value'];
                $type = empty( $meta_value['type'] ) ? false : $meta_value['type'];

                if ( $type != 'in_progress' ) {
                    continue;
                }

                $column_id = $meta['entity_id'];
                $meta_project = $meta['project_id'];
                $meta_value['users'] = empty( $meta_value['users'] ) ? [] : $meta_value['users'];

                if ( $meta_project == $project_id ) {
                    $has_task = Boardable::where('board_id', '!=', $column_id)
                        ->where('board_type', 'kanboard')
                        ->where('boardable_id', $task_id)
                        ->first();

                    if( $has_task ) {
                        $has_task->delete();
                    }

                    self::getInstance()->store_column_task( $column_id, $project_id, $task_id );
                    self::getInstance()->automaiton_add_users( $meta_value['users'], $task_id, $project_id );
                }
            }

        }
    }

    function automation( WP_REST_Request $request ) {
        $project_id = $request->get_param('project_id');
        $board_id = $request->get_param('board_id');
        $postdata = $request->get_param('data');
        $postdata = maybe_serialize( $postdata );

        $validation = $this->automation_validation_before_save( $request->get_params() );

        if ( is_wp_error( $validation ) ) {
            wp_send_json_error([
                'error' => $validation->get_error_message()
            ], 400);
        }

        pm_update_meta( $board_id, $project_id, 'kanboard', 'automation', $postdata );

        $this->set_automation_default_items( $request->get_params() );

        return $this->get_response(null);
    }

    public function automation_validation_before_save( $postData ) {
        $project_id = $postData['project_id'];
        $column_id = $postData['board_id'];

        $metas = \WeDevs\PM\Common\Models\Meta::where( 'project_id', $project_id )
            ->where( 'entity_type',  'kanboard' )
            ->where( 'meta_key', 'automation' )
            ->get()
            ->toArray();

        $data = $postData['data'];
        //pmpr($data, $metas); die();
        if ( $data['type'] == 'todo' ) {
            if ( $data['todo']['section'] == 'newlyadded' ) {
                foreach ( $metas as $key => $meta ) {

                    if( $column_id == $meta['entity_id'] ) {
                        continue;
                    }

                    if ( $meta['meta_value']['type'] == 'todo' ) {

                        if ( $meta['meta_value']['todo']['section'] == 'lists' ) {
                            if ( !empty( $meta['meta_value']['todo']['lists'] ) ) {
                                return new \WP_Error(
                                    'newlyadded',
                                    __( "Please remove the 'task lists' item from others column",
                                    "pm-pro"
                                    ) );
                            }
                        }

                        if ( $meta['meta_value']['todo']['section'] == 'newlyadded' ) {
                            return new \WP_Error(
                                'newlyadded',
                                __( "You have selectd 'Newlyadded' option for others column",
                                "pm-pro" )
                            );
                        }
                    }
                }
            }

            if ( $data['todo']['section'] == 'lists' ) {
                foreach ( $metas as $key => $meta ) {

                    if ( $column_id == $meta['entity_id'] ) {
                        continue;
                    }

                    if ( $meta['meta_value']['type'] == 'todo' ) {

                        if ( $meta['meta_value']['todo']['section'] == 'newlyadded' ) {
                            if ( !empty( $data['todo']['lists'] ) ) {
                                return new \WP_Error(
                                    'newlyadded',
                                    __( "Please remove the 'Newlyadded' option from others column", "pm-pro" )
                                );
                            }
                        }

                        if ( $meta['meta_value']['todo']['section'] == 'lists' ) {
                            $db_list_ids = wp_list_pluck( $meta['meta_value']['todo']['lists'], 'id' );
                            $req_list_ids = wp_list_pluck( $data['todo']['lists'], 'id' );

                            $diff = array_intersect($db_list_ids, $req_list_ids);

                            if ( ! empty( $diff ) ) {
                                $common_id = reset( $diff );
                                foreach ( $data['todo']['lists'] as $key => $list ) {
                                    if( $list['id'] == $common_id ) {
                                        return new \WP_Error( 'lists', __( "'" .$list['title']. "' has already assign in others column automation", "pm-pro" ) );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if ( $data['type'] == 'done' ) {

            foreach ( $metas as $key => $meta ) {
                if( $column_id == $meta['entity_id'] ) {
                    continue;
                }

                if ( $meta['meta_value']['type'] == 'done' ) {

                    if ( $meta['meta_value']['done']['completed'] == $data['done']['completed']
                        && $data['done']['completed'] != 'false'
                        && !empty($data['done']['completed']) ) {

                        return new \WP_Error( 'done', __(
                            "You have already selected 'completed tasks' in others column",
                            "pm-pro"
                            )
                        );
                    }
                }
            }
        }

        if ( $data['type'] == 'in_progress' ) {

            foreach ( $metas as $key => $meta ) {
                if( $column_id == $meta['entity_id'] ) {
                    continue;
                }

                if ( $meta['meta_value']['type'] == 'in_progress') {

                    if( $meta['meta_value']['inProgress']['reOpened'] == $data['inProgress']['reOpened'] && $data['inProgress']['reOpened'] != 'false' && !empty($data['inProgress']['reOpened']) ) {
                        return new \WP_Error( 'done', __( "You have selectd 'in progress' in others column", "pm-pro" ) );
                    }
                }
            }
        }

        if ( $data['taskStatus'] == 'completed' ) {

            foreach ( $metas as $key => $meta ) {
                if( $column_id == $meta['entity_id'] ) {
                    continue;
                }

                // if( $project_id != $meta['project_id'] ) {
                //     continue;
                // }

                if ( $meta['meta_value']['taskStatus'] == 'completed' ) {

                    return new \WP_Error( 'done', __( "You have already selectd 'completed task status' in others column", "pm-pro" ) );
                }
            }
        }

        if ( $data['taskStatus'] == 'incomplete' ) {

            foreach ( $metas as $key => $meta ) {
                if( $column_id == $meta['entity_id'] ) {
                    continue;
                }

                if ( $meta['meta_value']['taskStatus'] == 'incomplete' ) {

                    return new \WP_Error( 'done', __( "You have already selectd 'incomplete task status' in others column", "pm-pro" ) );
                }
            }
        }
    }

    function set_automation_default_items( $postData ) {
        $project_id = $postData['project_id'];
        $board_id = $postData['board_id'];

        $tasks = Global_Kanboard::with('tasks')
            ->find( $board_id )
            ->tasks()
            ->get()
            ->toArray();

        foreach ( $tasks as $key => $task ) {
            $params['project_id']       = $project_id;
            $params['section_id']       = $board_id;
            $params['dragabel_task_id'] = $task['id'];
            $params['is_move']          = 'yes';

            $this->check_automation( $params );
        }
    }

    public static function after_create_task( $task, $params ) {
        $project_id = empty( $params['project_id'] ) ? 0 : intval( $params['project_id'] );
        $kan_board_id = empty( $params['kan_board_id'] ) ? false : intval( $params['kan_board_id'] );


        //Create new task from kanboard and change their status
        if ( ! empty( $kan_board_id ) ) {
            $params['project_id']       = $project_id;
            $params['section_id']       = $kan_board_id;
            $params['dragabel_task_id'] = $task->id;
            $params['is_move']          = 'yes';
            self::getInstance()->check_automation( $params );
        }


        $metas = \WeDevs\PM\Common\Models\Meta::where( 'project_id', $project_id )
            ->where( 'entity_type',  'kanboard' )
            ->where( 'meta_key', 'automation' )
            ->get()
            ->toArray();

        foreach ( $metas as $key => $meta ) {
            $meta_value = $meta['meta_value'];
            $type = empty( $meta_value['type'] ) ? false : $meta_value['type'];

            $meta_value['users'] = empty( $meta_value['users'] ) ? [] : $meta_value['users'];

            if ( $type == 'todo' && !$kan_board_id ) {
                //create new task from list view and move them to kanboard
                switch ( $meta_value['todo']['section'] ) {
                    case 'lists':
                        self::automation_new_task_in_list( $task, $params, $meta );
                        //self::getInstance()->automaiton_add_users( $meta_value['users'], $task->id, $project_id );
                        break;

                    case 'newlyadded':
                        self::automation_newlyadded( $task, $params, $meta );
                        //self::getInstance()->automaiton_add_users( $meta_value['users'], $task->id, $project_id );
                        break;
                }
            }

            if ( $type == 'none' && ( $meta['entity_id'] == $kan_board_id ) ) {
                self::getInstance()->automaiton_add_users( $meta_value['users'], $task->id, $project_id );
            }

            if ( $type == 'in_progress' && ( $meta['entity_id'] == $kan_board_id ) ) {
                self::getInstance()->automaiton_add_users( $meta_value['users'], $task->id, $project_id );
            }

            if ( $type == 'done' && ( $meta['entity_id'] == $kan_board_id ) ) {
                self::getInstance()->automaiton_add_users( $meta_value['users'], $task->id, $project_id );
            }

        }
    }

    public function automaiton_add_users( $users, $task_id, $project_id ) {
        if ( ! empty( $users ) ) {
            $users_id = wp_list_pluck( $users, 'id' );

            $prev_users = Assignee::select('assigned_to')
                ->where('task_id', $task_id)
                ->where('project_id', $project_id)
                ->get()
                ->toArray();

            $prev_users = wp_list_pluck( $prev_users, 'assigned_to' );

            $users_id = array_unique( array_merge( $users_id, $prev_users ) );

            $task = [
                'task_id'    => $task_id,
                'assignees'  => $users_id
            ];

            Task_Controller::task_update( $task );
        }
    }

    public static function automation_new_task_in_list( $task, $params, $config ) {
        $meta_value = $config['meta_value'];
        $column_id = $config['entity_id'];
        $lists     = $config['meta_value']['todo']['lists'];
        $list_ids  = wp_list_pluck( $lists, 'id' );
        $list_id   = $params['board_id'];
        $task_id   = $task->id;
        $project_id = $task->project_id;
        $config_project = $config['project_id'];
        $meta_value['users'] = empty( $meta_value['users'] ) ? [] : $meta_value['users'];

        if ( in_array( $list_id, $list_ids ) &&  $config_project == $project_id ) {
            self::getInstance()->store_column_task( $column_id, $project_id, $task_id );
            self::getInstance()->automaiton_add_users( $meta_value['users'], $task_id, $project_id );
        }
    }

    public static function automation_newlyadded( $task, $params, $config ) {
        $column_id = $config['entity_id'];
        $meta_value = $config['meta_value'];
        //$lists     = $config['meta_value']['todo']['lists'];
        //$list_ids  = wp_list_pluck( $lists, 'id' );
        //$list_id   = $params['board_id'];
        $task_id   = $task->id;
        $project_id = $task->project_id;
        $config_project = $config['project_id'];
        $meta_value['users'] = empty( $meta_value['users'] ) ? [] : $meta_value['users'];

        if ( $config_project == $project_id ) {
            self::getInstance()->store_column_task( $column_id, $project_id, $task_id );
            self::getInstance()->automaiton_add_users( $meta_value['users'], $task_id, $project_id );
        }
    }

    /**
     * Task status cange to complete or incomplete from list view and move it kanboard according their status
     */
    public static function before_change_task_status( $task, $old_task, $params ) {

        $project_id = empty( $task->project_id ) ? 0 : intval( $task->project_id );
        $task_id    = $task->id;
        $status     = $task->status;

        // When task mark undone or reopen
        if ( $status == 'incomplete' ) {

            // Finding in_progress type columna and none type column and add this task
            $metas = \WeDevs\PM\Common\Models\Meta::where( 'project_id', $project_id )
                ->where( 'entity_type',  'kanboard' )
                ->where( 'meta_key', 'automation' )
                ->get()
                ->toArray();

            foreach ( $metas as $key => $meta ) {

                $meta_value          = $meta['meta_value'];
                $type                = empty( $meta_value['type'] ) ? false : $meta_value['type'];
                $meta_project        = $meta['project_id'];
                $meta_value['users'] = empty( $meta_value['users'] ) ? [] : $meta_value['users'];



                if ( $type == 'in_progress' ) {
                    if( $meta['meta_value']['inProgress']['reOpened'] != 'yes' ) {
                        continue;
                    }
                    $in_progress_column_id = $meta['entity_id'];


                    $has_task = Boardable::where('board_id', '!=', $in_progress_column_id)
                        ->where('board_type', 'kanboard')
                        ->where('boardable_id', $task_id)
                        ->first();

                    if( $has_task ) {
                        $has_task->delete();
                    }

                    if ( $meta_project == $project_id ) {

                        self::getInstance()->store_column_task( $in_progress_column_id, $project_id, $task_id );
                        self::getInstance()->automaiton_add_users( $meta_value['users'], $task_id, $project_id );
                    }

                }
            }

            return;

        }

        // When task mark undone or reopen
        if ( $status == 'complete' ) {

            //When task mark done
            $metas = \WeDevs\PM\Common\Models\Meta::where( 'project_id', $project_id )
                ->where( 'entity_type',  'kanboard' )
                ->where( 'meta_key', 'automation' )
                ->get()
                ->toArray();

            foreach ( $metas as $key => $meta ) {
                $column_id = $meta['entity_id'];
                $meta_value = $meta['meta_value'];
                $type = empty( $meta_value['type'] ) ? false : $meta_value['type'];
                $config_project = $meta['project_id'];


                if ( $type != 'done' ) {
                    continue;
                }

                if (
                    ( $meta_value['done']['completed'] == 'yes' )
                        &&
                    $config_project == $project_id
                )
                {

                    $column_id = $meta['entity_id'];
                    $has_task = Boardable::where('board_id', '!=', $column_id)
                        ->where('board_type', 'kanboard')
                        ->where('boardable_id', $task_id)
                        ->first();

                    if( $has_task ) {
                        $has_task->delete();
                    }

                    $meta_value['users'] = empty( $meta_value['users'] ) ? [] : $meta_value['users'];
                    self::getInstance()->store_column_task( $column_id, $project_id, $task_id );
                    self::getInstance()->automaiton_add_users( $meta_value['users'], $task->id, $project_id );
                }
            }
        }
    }

    public function header_background( WP_REST_Request $request ) {
        $project_id        = $request->get_param('project_id');
        $board_id          = $request->get_param('board_id');
        $header_background = $request->get_param('header_background');

        pm_update_meta( $board_id, $project_id, 'kanboard', 'header_background', $header_background );

        wp_send_json_success();
    }

    function search_tasks( WP_REST_Request $request ) {
        $tb_lists     = pm_tb_prefix() . 'pm_boards';
        $tb_tasks     = pm_tb_prefix() . 'pm_tasks';
        $task_ids     = [];

        $list_tasks = ( new Task_Controller )->filter_query( $request );
        $list_tasks = $list_tasks->get()->toArray();

        foreach ( $list_tasks as $lkey => $list ) {
            foreach ( $list['tasks'] as $tkey => $task ) {
                $task_ids[] = $task['id'];
            }
        }

        $boards = Global_Kanboard::select( $tb_lists. '.id' )
            ->where( $tb_lists . '.type', 'kanboard' )
            ->with(
                [
                    'tasks' => function($q) use( $tb_tasks, $task_ids ) {
                        $q->select( $tb_tasks . '.id as task_id' )
                            ->whereIn( $tb_tasks . '.id', $task_ids );
                    }
                ]
            )
            ->get()
            ->toArray();

        $tasks = ( new Task_Controller )->get_tasks( $task_ids, [
            'list_task_transormer_filter' => false
        ] );

        $tasks_by_id = [];

        foreach ( $tasks['data'] as $key => $task ) {
            $tasks_by_id[$task['id']] = $task;
        }

        foreach ( $boards as $bkey => $board ) {
            foreach ( $board['tasks'] as $tkey => $task ) {
                if ( empty( $tasks_by_id[$task['task_id']] ) ) {
                    continue;
                }

                $boards[$bkey]['tasks'][$tkey] = $tasks_by_id[$task['task_id']];
            }
        }

        wp_send_json_success( $boards );
    }

    function import_bulk_task( WP_REST_Request $request ) {
        $project_id = $request->get_param('project_id');
        $board_id   = $request->get_param('board_id');
        $items      = $request->get_param('items');

        $has_task = Boardable::select('boardable_id')
            ->where('board_type', 'kanboard')
            ->whereIn('boardable_id', $items)
            ->get()
            ->toArray();

        $has_items     = wp_list_pluck( $has_task, 'boardable_id' );
        $exclude_items = array_diff( $items,  $has_items );

        $datas = [];

        foreach ( $exclude_items as $task_id ) {
            $datas[] = [
                'board_id'       => $board_id,
                'board_type'     => 'kanboard',
                'boardable_id'   => $task_id,
                'boardable_type' => 'task',
                'order'          => 0,
                'created_by'     => get_current_user_id(),
                'updated_by'     => get_current_user_id(),
                'created_at'     => date( 'Y-m-d h:i:s', strtotime( current_time( 'mysql' ) ) ),
                'updated_at'     => date( 'Y-m-d h:i:s', strtotime( current_time( 'mysql' ) ) )
            ];
        }

        Boardable::insert( $datas );

        return wp_send_json_success();
    }

    public function get_projects( WP_REST_Request $request ) {
        global $wpdb;

        $all_projects = Project::where('status', 0)
            ->get()
            ->toArray();

        $preexisting_boardables = Boardable::where( 'board_type', 'kanboard' )
            ->where( 'boardable_type', 'project' )
            ->get()
            ->toArray();

        if ( empty( $boardables ) ) {
            wp_send_json_success( [] );
        };

        // Project Ids which are already on the kanban
        $gk_project_ids = wp_list_pluck( $results, 'id' );

        foreach ( $tasks['data'] as $key => $task ) {
            if ( in_array( $task['id'], $gk_project_ids ) ) {
                unset( $tasks['data'][$key] );
            }
        }

        wp_send_json_success( array_values( $tasks['data'] ) );
    }
}


