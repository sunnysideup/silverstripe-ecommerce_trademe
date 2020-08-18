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
    protected $debug = true;

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
        $path = ExportToTradeMeTask::file_location();
        $data = CSVFunctionality::convertToCSV($this->getData(), ',');
        //set file
        file_put_contents($path, $data);
        return SS_HTTPRequest::send_file($data, basename($fileName), 'text/csv');
    }

    /**
     * @return array
     */
    protected function getData(): array
    {
        user_error('You need to extend this method');
    }
}
