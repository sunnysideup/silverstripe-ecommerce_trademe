<?php


/**
 * Sends listings to trademe
 */
class ExportToTradeMeTask extends BuildTask
{
    protected $title = 'FTP Product CSV to trademe';

    protected $description = 'Takes CSV and images and sends them to trademe for listing';

    /**
     * @var string
     */
    private static $ftp_location = '';

    /**
     * @var string
     */
    private static $username = '';

    /**
     * @var string
     */
    private static $password = '';

    /**
     * @var string
     */
    private static $folder_to_upload_to = 'In';

    /**
     * Run
     */
    public function run($request)
    {
        $connection = ftp_connect($this->Config()->get('ftp_location'));
        if($connection) {
            $login = ftp_login(
                $connection,
                $this->Config()->get('username'),
                $this->Config()->get('password')
            );
            ftp_pasv ( $connection, true );
            if ($login) {

                $upload = ftp_put($connection, 'In/products.csv', self::file_location(), FTP_BINARY);

                if (! $upload) {
                    user_error('FTP upload failed!');
                } else {
                    echo 'OK!';
                }
            } else {
                user_error('Login attempt failed!');
            }

            ftp_close($connection);
        } else {
            user_error('We could not connect to FTP');
        }

    }


    public static function file_location() : string
    {
        $path = Director::baseFolder() . '/trademe_data/';
        Filesystem::makeFolder($path);

        return $path . 'products.csv';
    }

    public static function  url_location()
    {
        return str_replace(Director::baseFolder() .'/', Director::absoluteURL('/'), self::file_location());
    }
}
