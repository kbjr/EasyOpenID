<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Store Method
|--------------------------------------------------------------------------
|
| How should data be stored? Valid values include 'file' or 'database'.
|
*/
$config['store_method'] = 'file';

/*
|--------------------------------------------------------------------------
| FileStore Path
|--------------------------------------------------------------------------
|
| If using file storage, where should data be stored?
|
*/
$config['store_path'] = '/tmp/_php_consumer_test';

/*
|--------------------------------------------------------------------------
| Associations Table
|--------------------------------------------------------------------------
|
| If using database storage, the table where associations are to be stored.
|
*/
$config['associations_table'] = 'oid_associations';

/*
|--------------------------------------------------------------------------
| Nonces Table
|--------------------------------------------------------------------------
|
| If using database storage, the table where nonces are to be stored.
|
*/
$config['nonces_table'] = 'oid_nonces';

/*
|--------------------------------------------------------------------------
| Facebook Connect Data
|--------------------------------------------------------------------------
|
| If using the Facebook Connect portion of the OAuth library, use this app
| ID/config automatically (no init needed).
|
*/
$config['facebook_connect'] = array(
	'app_id' => null,
	'secret' => null,
	'cookie' => true
);


/* End of file openid.php */
/* Location: ./system/application/config/openid.php */
