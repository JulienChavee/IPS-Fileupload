<?php


namespace IPS\fileupload\modules\front\fileupload;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * upload
 */
class _upload extends \IPS\Dispatcher\Controller
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

		\IPS\Output::i()->title = 'File upload';

		/* Verify if has permission */
		$allowed_group = \IPS\Settings::i()->fileupload_allowed_group;

		if($allowed_group !== '*') {
			$allowed_group = explode(',', $allowed_group);

			if( !\IPS\Member::loggedIn()->inGroup( $allowed_group ) ) {
				\IPS\Output::i()->error( 'node_error_no_perm', 'no_perm', 403, '' );
			}
		}

		/* Verify if is moderator */
		$moderator_group = \IPS\Settings::i()->fileupload_moderator_group;

		if($moderator_group !== '*') {
			$moderator_group = explode( ',', $moderator_group );

			if( \IPS\Member::loggedIn()->inGroup( $moderator_group ) )
				$isModerator = true;
			else
				$isModerator = false;
		} else
			$isModerator = true;

		$form = new \IPS\Helpers\Form;

		$form->add(new \IPS\Helpers\Form\Upload( 'fileupload_uploadField', NULL, TRUE, array( 'storageExtension' => 'fileUploadStorage', 'multiple' => TRUE ) ) );

		$files = array();

		$data = array();

		if( \IPS\Member::loggedIn()->member_id ) {
			if(!$isModerator)
				$data = \IPS\Db::i()->select( '*', 'fileupload_file', array( 'member=?', \IPS\Member::loggedIn()->member_id), 'id DESC' );
			else
				$data = \IPS\Db::i()->select( '*', 'fileupload_file', '', 'id DESC' );
		}

		if ( $values = $form->values() ) {
			foreach($values['fileupload_uploadField'] as $k => $v)
				\IPS\Db::i()->insert( 'fileupload_file', array( 'file' => (string) $v, 'member' => \IPS\Member::loggedIn()->member_id ) );

			\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=fileupload' ), 'fileupload_upload_ok' );
		} else {
			\IPS\Output::i()->output = \IPS\Theme::i()->getTemplate( 'browse' )->upload(
				$form,
				$data,
				$isModerator
			);

		}
	}
}