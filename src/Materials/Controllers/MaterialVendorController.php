<?php
namespace WeDevs\PM\Materials\Controllers;

use Reflection;
use WP_REST_Request;
use League\Fractal;
use League\Fractal\Resource\Item as Item;
use League\Fractal\Resource\Collection as Collection;
use WeDevs\PM\Common\Traits\Transformer_Manager;
use WeDevs\PM\Common\Traits\Request_Filter;
use Carbon\Carbon;
use WeDevs\PM\Materials\Models\MaterialVendor;
use WeDevs\PM\Materials\Transformers\MaterialVendorTransformer;
use Illuminate\Database\Capsule\Manager as Capsule;

class MaterialVendorController {

    use Transformer_Manager, Request_Filter;

    public function show() {

        $orders = MaterialVendor::all();
        $resource = new Collection( $orders, new MaterialVendorTransformer );

        return $this->get_response( $resource );
    }

    public function store(WP_REST_Request $request) {
        $data = [
            'name'          => $request->get_param( 'name' ),
            'description' => $request->get_param( 'description' ),
            'phone'         => $request->get_param( 'phone' ),
            'email'           => $request->get_param( 'email' ),
            'address'      => $request->get_param( 'address' )
        ];

        $newVendor = MaterialVendor::create($data);
        $resource = new Item( $newVendor, new MaterialVendorTransformer );
        $message  = ['message' => 'New vendor has been added successfully'];

        return $this->get_response( $resource, $message );
    }

    public function update( WP_REST_Request $request ) {
        $board_id = $request->get_param( 'board_id' );
        $old_idx  = $request->get_param( 'old_idx' );
        $new_idx  = $request->get_param( 'new_idx' );

        $board_moved = MaterialVendor::find($board_id );

        if ( ! $board ) {
            return false;
        }

        $data = [
            'order' => $new_idx
        ];

        $board->update_model( $data );

        $resource = new Item( $board, new MaterialVendor_Transformer );

        $message = [
            'message' => 'Your trcking time was stop successfully'
        ];

        return $this->get_response( $resource, $message );
    }

    public function delete( WP_REST_Request $request ) {
        $id = $request->get_param( 'id' );

        $vendor = MaterialVendor::where( 'id', $id )->first();
        $this->delete_all_relation($vendor);
        $vendor->delete();

        $message = [
            'message' => 'Vendor deleted successfully'
        ];

        return $this->get_response(null, $message);
    }

    function delete_all_relation(MaterialVendor $vendor) {
        $vendor->materialOrders()->delete();
    }
}


