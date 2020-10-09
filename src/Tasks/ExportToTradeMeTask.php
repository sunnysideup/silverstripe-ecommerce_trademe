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
     * Run
     */
    public function run($request)
    {
        $ftp = new \FtpClient\FtpClient();
        $ftp->connect($this->Config('ftp_location'));
        $ftp->login(
            $this->Config('username'),
            $this->Config('password')
        );

        $ftp->putFromPath(self::file_location());
    }

    public static function file_location()
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
