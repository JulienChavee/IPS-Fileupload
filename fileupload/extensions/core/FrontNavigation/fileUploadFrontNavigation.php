<?php
/**
 * @brief		Front Navigation Extension: fileUploadFrontNavigation
 * @author		<a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/legal/standards/
 * @package		IPS Social Suite
 * @subpackage	File Upload
 * @since		23 Jul 2015
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\fileupload\extensions\core\FrontNavigation;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * Front Navigation Extension: fileUploadFrontNavigation
 */
class _fileUploadFrontNavigation extends \IPS\core\FrontNavigation\FrontNavigationAbstract
{
	/**
	 * Get Type Title which will display in the AdminCP Menu Manager
	 *
	 * @return	string
	 */
	public static function typeTitle()
	{
		return \IPS\Member::loggedIn()->language()->addToStack('frontnavigation_fileupload');
	}

	/**
	 * Can access?
	 *
	 * @return	bool
	 */
	public function canView()
	{
		$allowed_group = \IPS\Settings::i()->fileupload_allowed_group;

		if($allowed_group === '*')
			return TRUE;
		else {
			$allowed_group = explode(',', $allowed_group);
			
			if(\IPS\Member::loggedIn()->inGroup($allowed_group))
				return TRUE;
			else
				return FALSE;
		}
	}
	
	/**
	 * Get Title
	 *
	 * @return	string
	 */
	public function title()
	{
		return \IPS\Member::loggedIn()->language()->addToStack('frontnavigation_fileupload');
	}
	
	/**
	 * Get Link
	 *
	 * @return	\IPS\Http\Url
	 */
	public function link()
	{
		return \IPS\Http\Url::internal( "app=fileupload" );
	}
	
	/**
	 * Is Active?
	 *
	 * @return	bool
	 */
	public function active()
	{
		return \IPS\Dispatcher::i()->application->directory === 'fileupload';
	}

	/**
	 * Children
	 *
	 * @return	array
	 */
	public function children( $noStore=FALSE )
	{
		return array();
	}
}