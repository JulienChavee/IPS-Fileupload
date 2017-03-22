<?php
/**
 * @brief		File Storage Extension: fileUploadStorage
 * @author		<a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) 2001 - SVN_YYYY Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/legal/standards/
 * @package		IPS Social Suite
 * @subpackage	File Upload
 * @since		23 Jul 2015
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\fileupload\extensions\core\FileStorage;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * File Storage Extension: fileUploadStorage
 */
class _fileUploadStorage
{
	/**
	 * Count stored files
	 *
	 * @return	int
	 */
	public function count()
	{
		return \IPS\Db::i()->select( 'COUNT(*)', 'fileupload_file' )->first();
	}
	
	/**
	 * Move stored files
	 *
	 * @param	int			$offset					This will be sent starting with 0, increasing to get all files stored by this extension
	 * @param	int			$storageConfiguration	New storage configuration ID
	 * @param	int|NULL	$oldConfiguration		Old storage configuration ID
	 * @throws	\UnderflowException					When file record doesn't exist. Indicating there are no more files to move
	 * @return	void|int							An offset integer to use on the next cycle, or nothing
	 */
	public function move( $offset, $storageConfiguration, $oldConfiguration=NULL )
	{
		$file = \IPS\Db::i()->select( '*', 'fileupload_file', array( 'id > ?', $offset ), 'id', array( 0, 1 ) )->first();

		try
		{
			$file = \IPS\File::get( $oldConfiguration ?: 'fileupload_fileUploadStorage', $file['file'] )->move( $storageConfiguration );
	
			
			if ( (string) $file != $file['file'] )
			{
				\IPS\Db::i()->update( 'fileupload_file', array( 'file' => (string) $file ), array( 'id=?', $file['id'] ) );
			}
		}
		catch( \Exception $e )
		{
			/* Any issues are logged and the \IPS\Db::i()->update not run as the exception is thrown */
		}
		
		return $file['id'];
	}

	/**
	 * Check if a file is valid
	 *
	 * @param	\IPS\Http\Url	$file		The file to check
	 * @return	bool
	 */
	public function isValidFile( $file )
	{
	}

	/**
	 * Delete all stored files
	 *
	 * @return	void
	 */
	public function delete()
	{
		foreach( \IPS\Db::i()->select( '*', 'fileupload_file', 'file IS NOT NULL' ) as $file )
		{
			try
			{
				\IPS\File::get( 'fileupload_fileUploadStorage', $file['file'] )->delete();
			}
			catch( \Exception $e ){}
		}
	}
}