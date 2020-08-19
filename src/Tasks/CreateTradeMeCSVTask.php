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
        return SS_HTTPRequest::send_file($data, basename($pathToFile), 'text/csv');
    }

    /**
     * @return array
     */
    protected function getData(): array
    {
        user_error('You need to extend this method');
    }
}
