<?php


namespace IPS\fileupload\modules\admin\fileupload;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * setting
 */
class _setting extends \IPS\Dispatcher\Controller
{
	/**
	 * Execute
	 *
	 * @return	void
	 */
	public function execute()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'fileupload_settings_manage' );
		parent::execute();
	}

	/**
	 * ...
	 *
	 * @return	void
	 */
	protected function manage()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'fileupload_settings_manage' );
		
		$tabs = array();
		// if ( \IPS\Member::loggedIn()->hasAcpRestriction('forum_settings') )
		// {
		// 	$tabs['settings'] = 'forum_settings';
		// }
		// if ( \IPS\Member::loggedIn()->hasAcpRestriction('archive_manage') )
		// {
		// 	$tabs['archiving'] = 'archiving';
		// }

		$tabs['setting'] = "Settings";
		
		$activeTab = \IPS\Request::i()->tab ?: 'setting';
		$activeTabContents = call_user_func( array( $this, 'manage' . ucfirst( $activeTab ) ) );
		
		if( \IPS\Request::i()->isAjax() and !isset( \IPS\Request::i()->ajaxValidate ) )
		{
			\IPS\Output::i()->output = $activeTabContents;
		}
		else
		{		
			\IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack('menu__fileupload_setting');
			\IPS\Output::i()->output 	= \IPS\Theme::i()->getTemplate( 'global', 'core' )->tabs( $tabs, $activeTab, $activeTabContents, \IPS\Http\Url::internal( "app=fileupload&module=fileupload&controller=setting" ) );
		}
	}

	/**
	 * Settings
	 *
	 * @return	string
	 */
	protected function manageSetting()
	{
		\IPS\Dispatcher::i()->checkAcpPermission( 'fileupload_settings_manage' );

		$form = new \IPS\Helpers\Form;
		$form->addHeader('fileupload_general_setting');

		$form->add( new \IPS\Helpers\Form\Select( 'fileupload_allowed_group', ( \IPS\Settings::i()->fileupload_allowed_group !== NULL ) ? ( ( \IPS\Settings::i()->fileupload_allowed_group ===  '*' ) ? '*' : explode( ',', \IPS\Settings::i()->fileupload_allowed_group ) ) : NULL, FALSE, array( 'options' => \IPS\Member\Group::groups(), 'unlimited' => '*', 'multiple' => TRUE, 'sort' => TRUE, 'parse' => 'normal' ) ) );

		$form->addHeader('fileupload_moderator_setting');

		$form->add( new \IPS\Helpers\Form\Select( 'fileupload_moderator_group', ( \IPS\Settings::i()->fileupload_moderator_group !== NULL ) ? ( ( \IPS\Settings::i()->fileupload_moderator_group ===  '*' ) ? '*' : explode( ',', \IPS\Settings::i()->fileupload_moderator_group ) ) : NULL, FALSE, array( 'options' => \IPS\Member\Group::groups(), 'unlimited' => '*', 'multiple' => TRUE, 'sort' => TRUE, 'parse' => 'normal' ) ) );

		if ( $values = $form->values() )
		{
			if($values['fileupload_allowed_group'] !== '*')
				$values['fileupload_allowed_group'] = implode(',', $values['fileupload_allowed_group']);

			if($values['fileupload_moderator_group'] !== '*')
				$values['fileupload_moderator_group'] = implode(',', $values['fileupload_moderator_group']);

			$form->saveAsSettings( $values );
			\IPS\Session::i()->log( 'acplogs__fileupload_setting_permission_change' );
			\IPS\Output::i()->redirect( \IPS\Http\Url::internal( 'app=fileupload&module=fileupload&controller=setting&tab=setting' ), 'saved' );
		}
		
		return $form;
	}
	
	// Create new methods with the same name as the 'do' parameter which should execute it
}