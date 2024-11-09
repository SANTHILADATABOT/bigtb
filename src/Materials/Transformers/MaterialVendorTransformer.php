<?php

namespace WeDevs\PM\Materials\Transformers;

use League\Fractal\TransformerAbstract;
use WeDevs\PM\Common\Traits\Resource_Editors;
use WeDevs\PM\Materials\Models\MaterialVendor;

class MaterialVendorTransformer extends TransformerAbstract {

    use Resource_Editors;

    public function transform( MaterialVendor $item ) {
        return [
            'id'               => $item->id,
            'name'         => $item->name,
            'description'=> $item->description,
            'phone'        => $item->phone,
            'email'          => $item->email,
            'address'     => $item->address,
        ];
    }
}
