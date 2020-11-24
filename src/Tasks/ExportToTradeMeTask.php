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
        $ftp = new \FtpClient\FtpClient();
        $ftp->connect($this->Config()->get('ftp_location'));
        $ftp->login(
            $this->Config()->get('username'),
            $this->Config()->get('password')
        );
        $ftp->chdir($this->Config()->get('folder_to_upload_to'));
        $ftp->putFromPath(self::file_location());
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
