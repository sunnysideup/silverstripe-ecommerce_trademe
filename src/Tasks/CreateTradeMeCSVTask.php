<?php


/**
 * create CSV for TradeMe
 */
class CreateTradeMeCSVTask extends BuildTask
{

    private const SUBTITLE = 'NZ Based Company – Full manufactures Warranty – 30+ years in business';

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
        $bestOptionArray = [];
        foreach($imageCollection as $key => $image) {
            if($image) {
                $size = (int) $image->getWidth() * (int) $image->getHeight();
                $link = '';
                if($image->getWidth() >= $this->minImageWidth && $image->getHeight() >= $this->minImageHeight) {
                    $link = $image->AbsoluteLink();
                } elseif($image->getWidth()) {
                    $link = $image->Pad($this->minImageWidth, $this->minImageHeight)->Link();
                    if($this->debug) {
                        DB::alteration_message(
                            '
                                ---- <a href="'.$image->AbsoluteLink().'">Image for '.$product->Title.' ('.$product->InternalItemID.')</a> is too small for TradeMe.
                                Please upload a bigger image.
                                The minimum size is is: '.$this->minImageWidth.'px x '.$this->minImageHeight.'px,
                                the image is: '.$image->getWidth() .'px  x '.$image->getHeight() .'px.
                            ',
                            'deleted'
                        );
                    }
                }
                if($link) {
                    $fileNames[$size] = $link;
                }
            }
        }
        krsort($fileNames, SORT_NUMERIC);
        return $fileNames;
    }
}
