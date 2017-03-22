<?php


namespace IPS\fileupload\modules\front\fileupload;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * delete
 */
class _delete extends \IPS\Dispatcher\Controller
{
	/**
	 * Execute
	 *
	 * @return	void
	 */
	public function execute()
	{
		
		parent::execute();
	}

	/**
	 * ...
	 *
	 * @return	void
	 */
	protected function manage()
	{
		// This is the default method if no 'do' parameter is specified

		\IPS\Session::i()->csrfCheck();

		/* Can delete files */
		$allowed_group = \IPS\Settings::i()->fileupload_moderator_group;
		if($allowed_group !== '*' && !\IPS\Member::loggedIn()->inGroup( explode( ',', $allowed_group ) ) )
			\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=fileupload' ) );		

		/* Delete file */
		if ( empty( \IPS\Request::i()->id ) )
			\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=fileupload' ) );
		else {
			try {
				$data = \IPS\Db::i()->select( '*', 'fileupload_file', array( 'id=?', \IPS\Request::i()->id ) )->first();

				$file = \IPS\File::get( 'fileupload', $data['file']);

				if( $file->delete() ) {
					\IPS\Db::i()->delete( 'fileupload_file', array( 'id=?', \IPS\Request::i()->id ) );
					\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=fileupload' ), 'fileupload_file_deleted' );
				} else
					\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=fileupload' ), 'fileupload_file_delete_error' );
			}
			catch (\UnderflowException $e) {
				\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=fileupload' ), 'fileupload_file_delete_error' );
			}
		}
	}
	
	// Create new methods with the same name as the 'do' parameter which should execute it
}