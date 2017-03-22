<?php
require_once str_replace( 'applications/fileupload/interface/file/file.php', '', str_replace( '\\', '/', __FILE__ ) ) . 'init.php';
\IPS\Session\Front::i();

try
{	
	/* Get attachment */
	$file_db = \IPS\Db::i()->select( '*', 'fileupload_file', array( 'id=?', \IPS\Request::i()->id ) )->first();

	/* Get file and data */
	$file		= \IPS\File::get( 'fileupload_fileUploadStorage', $file_db['file'] );
	$headers	= array_merge( \IPS\Output::getCacheHeaders( time(), 360 ), array( "Content-Disposition" => \IPS\Output::getContentDisposition( 'attachment', $file->originalFilename ), "X-Content-Type-Options" => "nosniff" ) );

	if( $file->filename == \IPS\Request::i()->filename ) {
	
		/* Send headers and print file */
		\IPS\Output::i()->sendStatusCodeHeader( 200 );
		\IPS\Output::i()->sendHeader( "Content-type: " . \IPS\File::getMimeType( $file->originalFilename ) . ";charset=UTF-8" );

		foreach( $headers as $key => $header )
		{
			\IPS\Output::i()->sendHeader( $key . ': ' . $header );
		}
		\IPS\Output::i()->sendHeader( "Content-Length: " . $file->filesize() );

		$file->printFile();
		exit;
	} else {
		throw new \ErrorException("Error Processing Request", 1);
		
	}
}
catch ( \UnderflowException $e )
{
	/* Remove previously sent headers, so that the browser doesn't try to download this error as a file */
	header_remove();
	\IPS\Dispatcher\Front::i();
	\IPS\Output::i()->error( 'node_error', '2S328/1', 404, '' );
}
catch ( \ErrorException $e )
{
	/* Remove previously sent headers, so that the browser doesn't try to download this error as a file */
	header_remove();
	\IPS\Dispatcher\Front::i();
	\IPS\Output::i()->error( 'node_error', '2C327/1', 404, '' );
}