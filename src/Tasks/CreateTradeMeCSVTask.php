<?php


/**
 * create CSV for TradeMe
 */
class CreateTradeMeCSVTask extends BuildTask
{
    /**
     * @var bool
     */
    protected $enabled = false;

    /**
     * @inherit
     */
    protected $title = 'Build CSV for trade me';

    /**
     * @inherit
     */
    protected $description = 'Builds a CSV with all information required for trademe to create listings';

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @var int
     */
    protected $minImageWidth = 800;

    /**
     * @var int
     */
    protected $minImageHeight = 600;

    /**
     * Run
     */
    public function run($request)
    {
        increase_time_limit_to(600);
        //get details
        $pathToFile = ExportToTradeMeTask::file_location();
        $data = CSVFunctionality::convertToCSV($this->getData(), ',');
        //set file
        file_put_contents($pathToFile, $data);
        $this->debug = empty($_GET['debug']) ? false : true;
        echo '<h1><a href="'.ExportToTradeMeTask::url_location().'">Download Results</a></h1>';
    }

    /**
     * @return array
     */
    protected function getData(): array
    {
        user_error('You need to extend this method');
    }
}
