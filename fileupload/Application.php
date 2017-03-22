<?php
/**
 * @brief		File Upload Application Class
 * @author		<a href=''>Jujuwar</a>
 * @copyright	(c) 2015 Jujuwar
 * @package		IPS Social Suite
 * @subpackage	File Upload
 * @since		22 Jul 2015
 * @version		
 */
 
namespace IPS\fileupload;

/**
 * File Upload Application Class
 */
class _Application extends \IPS\Application
{	
	/**
	 * Init
	 *
	 * @return	void
	 */
	public function init()
	{	
		/* If the viewing member cannot view the board (ex: guests must login first), then send a 404 Not Found header here, before the Login page shows in the dispatcher */
		if ( !\IPS\Member::loggedIn()->group['g_view_board'] and ( \IPS\Request::i()->module == 'fileupload' and \IPS\Request::i()->controller == 'fileupload' ) )
		{
			\IPS\Output::i()->error( 'node_error', '2F219/1', 404, '' );
		}
	}

	/**
	 * [Node] Get Icon for tree
	 *
	 * @note	Return the class for the icon (e.g. 'globe')
	 * @return	string|null
	 */
	protected function get__icon()
	{
		return 'upload';
	}
}