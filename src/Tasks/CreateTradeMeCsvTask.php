<?php


/**
 * create CSV for TradeMe
 */
class CreateTradeMeCsvTask extends BuildTask
{

    //private const SUBTITLE = 'NZ Based Company – Full manufactures Warranty – 30+ years in business';

    /**
     * array of fields for TradeMe and their default values
     * if no default value is set then you will have to set one
     * in the method using a variable with the same name ... e.g. $sku
     * @var array
     */
    protected $fields = [
        'sku' => null,
        'title' => null,
        'subtitle' => null,
        'description' => null,
        'categoryId' => null,
        'buyNowPrice' => null,
        'buyNowOnly' => 1,
        'stockLevel' => null,
        'isGallery' => 0,
        'isNew' => 1,
        'allowCreditCard' => 1,
        'allowAfterpay' => null,
        'sendPaymentInstructions' => 0,
        'shippingPrice1' => null,
        'shippingDescription1' => null,
        'imageFileName1' => null,
        'brandTitle' => null,
        'attributeName1' => null,
        'attributeName2' => null,
        'attributeName3' => null,
        'attributeValue1' => null,
        'attributeValue2' => null,
        'attributeValue3' => null,
    ];

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
            echo '<h4>Add ?details=1 to your URL to see all the details on screen...</h4>';
        }
        //get details
        $pathToFile = ExportToTradeMeTask::file_location();
        $data = CsvFunctionality::convertToCSV($this->getData(), ',');
        //set file
        file_put_contents($pathToFile, $data);
        echo '<h1><a href="'.ExportToTradeMeTask::url_location().'">Download Results</a></h1>';
        if(Director::isLive()) {
            echo '<h1>NEXT: <a href="/dev/tasks/ExportToTradeMeTask/">Export data to to TradeMe</a></h1>';
        }
    }

    /**
     * @return array
     */
    protected function getData(): array
    {
        $array = [];
        $array[] = array_keys($this->fields);
        $categoryListings = [];
        $countForReal = 0;

        $products =  TradeMeAssignProductController::base_list()
            ->filter(['ID' => $this->getIDsOfProducts()])
            ->sort('InternalItemID');
        DB::alteration_message('There are ' . $products->count() . ' potential products to be listed on TradeMe.', 'created');
        foreach($products as $product) {
            $data = $product->getTradeMeData($this->fields);
            foreach($data['Data'] as $key => $value) {
                $innerArray[] = CsvFunctionality::removeBadCharacters($value);
            }
            if(!empty($data['Include'])) {
                if(empty($categoryListings[$data['TradeMeCategory']])) {
                    $categoryListings[$data['TradeMeCategory']] = [];
                }
                $categoryListings[$data['TradeMeCategory']][] = $product->InternalItemID.' - '.$product->Title;
                $countForReal++;
            }
            if(! empty($data['HasError'])) {
                if(! isset($data['HasError'])) {
                    $data['HasError'] = 'Unknown Error';
                }
                DB::alteration_message('Error: '.$data['ErrorMessage'], 'deleted');
            }
            $array[] = $innerArray;
        }

        DB::alteration_message('
            After review, there are ' . $countForReal . ' products that will actually be listed on TradeMe,
            The rest are out of stock or have some other issue.
            Below is a list by TradeMe Category.',
            'created'
        );

        echo '<h1>By Category</h1>';
        ksort($categoryListings);
        echo $this->arrayToHtml($categoryListings);

        return $array;
    }




    /**
     * we use this method to pre-select products that are eligible:
     * see WHERE statement for LOGIC
     */
    protected function getIDsOfProducts() : array
    {

        //get all the products that are applicable
        $sql = '
            SELECT
                "SiteTree_Live"."ID" ProductID
            FROM
                "SiteTree_Live"
            INNER JOIN
                "SiteTree_Live" AS ParentSiteTree ON "ParentSiteTree"."ID" = "SiteTree_Live"."ParentID"
            INNER JOIN
                "ProductGroup_Live" AS ParentProductGroup ON ParentSiteTree.ID = ParentProductGroup.ID
            INNER JOIN
                "Product_Live" AS Product ON "SiteTree_Live"."ID" = "Product_Live"."ID"
            WHERE
                "Product"."AllowPurchase" = 1 AND "Product"."ShowOnTradeMe" <> \'never\' AND "ParentProductGroup"."ListProductsOnTradeMe" <> \'none\'
                AND (
                    "Product"."ShowOnTradeMe" = \'always\'
                    OR
                    "ParentProductGroup"."ListProductsOnTradeMe" = \'all\'
                )
            ;
        ';
        $rows = DB::query($sql);
        $array = [0];
        foreach($rows as $row) {
            $array[row['ProductID']] = $row['ProductID'];
        }
        return $array;
    }


    public function getBestImage($product) : string
    {
        $tradeMeImage = $product->TradeMeImage();
        if($tradeMeImage && $tradeMeImage->exists()) {
            $link = $tradeMeImage->Link();
        } else {
            $list = $this->getBestImages($product);

            $link = !empty($list) ? array_shift($list) : '';
            if($link) {
                $link = Director::absoluteUrl($link);
            }
        }

        return $link;
    }

    protected function getBestImages($product) : array
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
                }
                if($link) {
                    $fileNames[$size] = $link;
                }
            }
        }
        krsort($fileNames, SORT_NUMERIC);
        return $fileNames;
    }

    protected function arrayToHtml( array $array ) : string
    {
        $html = '<ul>';
        foreach ( $array as $key => $value ) {
            if(is_array($value)) {
                $html .= '<li>' . $key . ':' . $this->arrayToHtml($item) . '</li>';
            } else {
                $html .= '<li>' . $value . '</li>';
            }
        }
        $html .= '</ul>';

        return $html;
    }

}
