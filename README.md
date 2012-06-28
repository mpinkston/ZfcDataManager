ZfcDataManager
==============

This is another PHP ORM that leverages many of the nifty features made available in Zend Framework 2

    'data' => array(
        'support' => array(
            'models' => array(
                'building' => array(
//                    'entity' => 'Support\Entity\Building',
//                    'hydrator' => 'Zend\Stdlib\Hydrator\ClassMethods',
                    'fields' => array(
                        'id' => array( 'mapping' => 'ip_id' ),
                        'name' => array( 'mapping' => 'ip_name' ),
                        'city' => array( 'mapping' => 'ip_city' ),
                        'state' => array( 'mapping' => 'ip_state' ),
                        'zip' => array( 'mapping' => 'ip_zip' ),
                        'units' => array(
                            'type' => 'store',
                            'mapping' => 'units',
                            'primary_key' => 'ip_id',
                            'foreign_key' => 'ipu_ip_id'
                        )
                    )
                ),
                'unit' => array(
                    'fields' => array(
                        'id' => array( 'mapping' => 'ipu_id' ),
                        'name' => array( 'mapping' => 'ipu_name' )
                    )
                )
            ),
            'stores' => array(
                'buildings' => array(
                    'page_size' => 20,
                    'model' => 'building',
                    'proxy' => 'support_db',
                    'mapping' => 'ics_properties'
                ),
                'units' => array(
                    'model' => 'unit',
                    'proxy' => 'support_db',
                    'mapping' => 'ics_property_units'
                )
            ),
            'proxies' => array(
                'support_db' => array(
                    'type' => 'ZfcDataManager\Proxy\Db\TableGatewayProxy',
                    'adapter' => 'Zend\Db\Adapter\Adapter'
                )
            )
        )
    ),
