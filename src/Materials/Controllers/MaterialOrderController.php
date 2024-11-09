<?php
namespace WeDevs\PM\Materials\Controllers;

use Reflection;
use WP_REST_Request;
use League\Fractal;
use League\Fractal\Resource\Item as Item;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use WeDevs\PM\Common\Traits\Transformer_Manager;
use WeDevs\PM\Project\Models\Project;
use WeDevs\PM\Common\Traits\Request_Filter;
use Carbon\Carbon;
use WeDevs\PM\Materials\Models\MaterialOrder;
use WeDevs\PM\Materials\Transformers\MaterialOrderTransformer;
use Illuminate\Database\Capsule\Manager as Capsule;
use WeDevs\PM\Project\Helper\Project as Project_Helper;

class MaterialOrderController {

    use Transformer_Manager, Request_Filter;

    public function show() {

        $orders = MaterialOrder::all();
        $resource = new Collection( $orders, new MaterialOrderTransformer );

        return $this->get_response( $resource );
    }

    public function store(WP_REST_Request $request) {
        $data = [
            'vendor_id'      => $request->get_param( 'vendor_id' ),
            'cost'               => $request->get_param( 'cost' ),
            'title'                => $request->get_param( 'title' ),
            'description'    => $request->get_param( 'description' ),
            'date'               => $request->get_param( 'date' ),
            'ordered_by'   => $request->get_param( 'ordered_by' ),
        ];

        $projects = $request->get_param( 'associated_projects' );

        // Create the MaterialOrder
        $newOrder = MaterialOrder::create($data);

        // Attach the projects to the MaterialOrder
        $newOrder->projects()->attach($projects);

        $resource = new Item( $newOrder, new MaterialOrderTransformer );
        $message  = ['message' => 'New order has been added successfully'];

        return $this->get_response( $resource, $message );
    }

    public function update( WP_REST_Request $request ) {
        $board_id = $request->get_param( 'board_id' );
        $old_idx  = $request->get_param( 'old_idx' );
        $new_idx  = $request->get_param( 'new_idx' );

        $board_moved = MaterialOrder::find($board_id );

        if ( ! $board ) {
            return false;
        }

        $data = [
            'order' => $new_idx
        ];

        $board->update_model( $data );

        $resource = new Item( $board, new MaterialOrder_Transformer );

        $message = [
            'message' => 'Your trcking time was stop successfully'
        ];

        return $this->get_response( $resource, $message );
    }

    public function delete( WP_REST_Request $request ) {
        $id = $request->get_param( 'id' );

        $order = MaterialOrder::where( 'id', $id )->first();
        $order->delete();

        $message = [
            'message' => 'Order deleted successfully'
        ];

        return $this->get_response(null, $message);
    }
}


