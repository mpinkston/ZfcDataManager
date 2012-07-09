ZfcDataManager (pre-alpha)
==============

ZfcDataManager is a PHP ORM originally inspired by ExtJS 4's Ext.Data package and built for Zend Framework 2

Purpose
--
The purpose of this project is to create an ORM that is simple to configure, simple to use, and data-source agnostic. 


Introduction
--
This project was originally inspired by the Ext.Data component included in Sencha's ExtJS 4 client-side web application framework. The Ext.Data package enables the developer to define models, model associations, and collections of models without worrying about the underlying data sources. 

I wanted to be able to do roughly the same thing with PHP, but most existing ORM libraries are pretty tightly integrated with some underlying database. Why shouldn't I be able to work with data from arbitrary sources in the same way?

So, this library should theoretically allow objects to be mapped to any programmable datasource (not just DBMSs). 

Theory
--
The basic theory behind the library is that you create your own entity (or not), define a model and list of fields to manage that entity, assign the model to a store, and tell the store what proxy to use for the data.  

You then use the store to access models, or collections of models, and ideally work with them as you would your own business object (entity). The model attempts to be transparent by proxying method calls and parameter access through to the entity while simultaneously triggering any relevant events (all configurable).  

The entity's dirty/clean state can be monitored by comparing its values to the values in the model's fields. A save() operation would then persist the data and refresh the field values.

Just about everything is lazy. A field that contains a model/store won't load it until asked.


#### Entities:
An entity is whatever you make it. It's your business object, so it's your business.  
*However*, if you choose not to define an Entity, the data manager will still attempt to return a stdClass instance with your data. (good for prototyping systems)

#### Models:
* Proxies to an Entity
* Contain fields
* Triggers events

#### Fields:
* Contains the original value from the proxy
* Can be a native PHP type, a Model, or a Store

#### Stores:
* Maps a model to a Proxy
* Manages a collection of models (pagination, etc)

#### Proxies:
* Maps data to a model
* Implements one or more of ReadableProxy, WritableProxy, SortableProxy, FilterableProxy *The store will attempt to intelligently deal with read/write/sort/filter based on the capabilities of the proxy*

Installation/Configuration
--
This is all alpha, so straight to the chase:

1. Install ZfcDataManager (should pretty much be like any other zf2 module..)
2. Add a global config for the data manager:

        return array(
        	'data' => array(
        		'default' => array(
        			'models' => array(
        			),
        			'stores' => array(
        			),
        			'proxies' => array(
        			)
        		)
        	)
			...
		)
*'default' can be changed to whatever you like by extending ZfcDataManager\Service\AbstractDataManagerFactory, overriding the 'getName' method and configuring a ServiceManager alias (just like Navigation)*

3. Define some models, stores, and proxies. (I'll document all the options later, but the following config has most options and is hopefully fairly intuitive)

		return array(
			'data' => array(
				'default' => array(
					'models' => array(
		                'complex' => array(
		                	'entity' => 'Support\Entity\Complex', // Define an entity to be used
		                	'hydrator' => 'Zend\StdLib\Hydrator\ClassMethods', // Define how the entity should be hydrated
		                    'fields' => array(
		                        'id' => array( 
		                        	'mapping' => 'ipc_id' // 'mapping' always gets passed to the proxy (proxy decides how to use it)
		                        ), 
		                        'name' => array( 'mapping' => 'ipc_name' )
		                    )
		                ),
		                'building' => array(
		                    'fields' => array(
		                        'id' => array( 'mapping' => 'ip_id' ),
		                        'name' => array( 'mapping' => 'ip_name' ),
		                        'complex' => array(
		                            'type' => 'model', // This field will contain a named model
		                            'store_name' => 'complexes',
		                            'primary_key' => 'ip_ipc_id',
		                            'foreign_key' => 'ipc_id'
		                        ),
		                        'units' => array(
		                            'type' => 'store', // and this one a store
		                            'store_name' => 'units',
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
		                'complexes' => array(
		                    'model' => 'complex',
		                    'proxy' => 'support_db',
		                    'mapping' => 'ics_property_complexes' // 'mapping' is used by the proxy
		                ),
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
			)
		)

*So far the only semi-implemented proxy is the TableGatewayProxy (which uses Zend\Db\TableGateway\TableGateway.)*  

*Currently, the AbstractProxy extends Zend\StdLib\AbstractOptions, and all config options other than 'type' will be passed to setFromArray*

Usage
--
1. Get an instance of the data manager. The default manager has a ServiceLocator alias of 'DataManager' (defined in Module.php).


		$sl = $this->getServiceLocator(); // Assuming this is available here..
		$dm = $sl->get('DataManager');
		
		// Now you can get the buildings store. (A magic method will look it up)
		$buildings = $dm->getBuildings();
		
		// Everything is lazy loaded, so the proxy won't be asked for data
		// until the store needs it. This will only fetch the building with id=5
		$aBuilding = $buildings->getById('5');

		// An individual model can be retrieved as well. (Also via magic method)
		$sameBuilding = $dm->getBuilding(5);

		
		// Currently this will retrieve and iterate the first 20 buildings.
		// (more thought to be put into this..)		
		foreach ($buildings as $building) {
			// .. do something
		}
		

To Do
--
Lots.. But so far, it's working pretty well for me

Here's what I'm currently mulling about:
* Allowing model definitions to be defined via annotations in the entity (maybe even auto-defer to Doctrine, etc. where appropriate?)
* Pagination: I don't want to have to demand view partials, but they certainly need to be an option. (or is this biting off more than I care to chew?)

* More built-in proxies. (of course finish the TableGateway one, and add SNMP and LDAP 'cause I need those for my project)

* Lots more tuning and api cleanups. 