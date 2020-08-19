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
        $this->debug = empty($_GET['details']) ? false : true;
        if(! $this->debug) {
            echo '<h1>Add ?details=1 to your URL to see all the details on screen...</h1>';
        }
        //get details
        $pathToFile = ExportToTradeMeTask::file_location();
        $data = CSVFunctionality::convertToCSV($this->getData(), ',');
        //set file
        file_put_contents($pathToFile, $data);
        echo '<h1><a href="'.ExportToTradeMeTask::url_location().'">Download Results</a></h1>';
        if(Director::isLive()) {
            echo '<h1><a href="/dev/tasks/ExportToTradeMeTask/">Export data to to TradeMe</a></h1>';
        }
    }

    /**
     * @return array
     */
    protected function getData(): array
    {
        user_error('You need to extend this method');
    }

    protected function getFirstImage($product) : string
    {
        $list = $this->getImageCollections($product);

        return !empty($list) ? array_shift($list) : '';
    }

    protected function getImageCollections($product) : array
    {
        $imageCollection = [
            $product->Image()
        ];
        foreach($product->AdditionalImages() as $image) {
            $imageCollection[] = $image;
        }
        $fileNames = [];
        foreach($imageCollection as $image) {
            if($image) {
                if($image->getWidth() >= $this->minImageWidth && $image->getHeight() >= $this->minImageHeight) {
                    $fileNames[] = $image->AbsoluteLink();
                } else {
                    if($this->debug) {
                        DB::alteration_message(
                            '
                                ---- Image '.$image->AbsoluteLink().' for '.$product->InternalItemID.' is too small for TradeMe.
                                Please upload a bigger image.
                                The Minimum Width is: '.$this->minImageWidth.', the image is: '.$image->getWidth() .'.
                                The Minimum Height is: '.$this->minImageHeight.', the image is: '.$image->getHeight() .'.
                            ',
                            'deleted'
                        );
                    }
                }
            }
        }
        return $fileNames;
    }
}
