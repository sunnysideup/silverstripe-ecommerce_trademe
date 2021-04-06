<?php

namespace Sunnysideup\EcommerceTrademe\Tasks;

use SilverStripe\Control\Director;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DB;
use Sunnysideup\EcommerceTrademe\Api\CsvFunctionality;
use Sunnysideup\EcommerceTrademe\Control\TradeMeAssignProductController;

/**
 * create CSV for TradeMe.
 */
class CreateTradeMeCsvTask extends BuildTask
{
    public const MAX_IMAGES = 7;

    //private const SUBTITLE = 'NZ Based Company – Full manufactures Warranty – 30+ years in business';

    /**
     * array of fields for TradeMe and their default values
     * if no default value is set then you will have to set one
     * in the method using a variable with the same name ... e.g. $sku.
     *
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
        'allowBankTransfer' => 1,
        'allowCreditCard' => null,
        'allowAfterpay' => null,
        'allowBankTransfer' => 1,
        'allowCreditCard' => null,
        'sendPaymentInstructions' => 0,
        'shippingPrice1' => null,
        'shippingDescription1' => null,
        'imageFileName1' => null,
        'imageFileName2' => null,
        'imageFileName3' => null,
        'imageFileName4' => null,
        'imageFileName5' => null,
        'imageFileName6' => null,
        'imageFileName7' => null,
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
    protected $enabled = true;

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

    protected $verbose = true;

    protected $html = '';

    public function setVerbose(bool $b)
    {
        $this->verbose = $b;

        return $this;
    }

    public function getHtml()
    {
        return $this->html;
    }

    /**
     * Run.
     *
     * @param mixed $request
     */
    public function run($request)
    {
        Silverstripe\Core\Environment::increaseTimeLimitTo(600);
        $this->debug = empty($_GET['details']) ? false : true;
        if (! $this->debug) {
            $this->output('<h4>Add ?details=1 to your URL to see all the details on screen...</h4>');
        }
        //get details
        $pathToFile = ExportToTradeMeTask::file_location();
        $data = CsvFunctionality::convertToCSV($this->getData(), ',');
        //set file
        file_put_contents($pathToFile, $data);
        $this->output('<h1><a href="' . ExportToTradeMeTask::url_location() . '">Download Results</a></h1>');
        if (Director::isLive()) {
            $this->output('<h1>NEXT: <a href="/dev/tasks/ExportToTradeMeTask/">Export data to to TradeMe</a></h1>');
        }
    }

    public function getBestImage($product): string
    {
        $list = $this->getBestImages($product);

        return empty($list) ? '' : array_shift($list);
    }

    public function getBestImages($product): array
    {
        $imageCollection = [
            $product->TradeMeImage(),
            $product->Image(),
        ];
        foreach ($product->AdditionalImages() as $image) {
            $imageCollection[] = $image;
        }
        $fileNames = [];
        foreach ($imageCollection as $image) {
            if ($image && $image->exists()) {
                $link = '';
                if ($image->getWidth() >= $this->minImageWidth && $image->getHeight() >= $this->minImageHeight) {
                    $link = $image->AbsoluteLink();
                } elseif ($image->getWidth()) {
                    $link = $image->Pad($this->minImageWidth, $this->minImageHeight)->Link();
                }
                if ($link) {
                    $link = Director::absoluteUrl($link);
                    $fileNames[] = $link;
                }
            }
        }

        return $fileNames;
    }

    protected function getData(): array
    {
        $array = [];
        $array[] = array_keys($this->fields);
        $categoryListings = [];
        $countForReal = 0;

        $products = TradeMeAssignProductController::base_list()
            ->filter(['ID' => $this->getIDsOfProducts()])
            ->sort('InternalItemID')
        ;
        $this->output('There are ' . $products->count() . ' potential products to be listed on TradeMe.', 'good');
        foreach ($products as $product) {
            $innerArray = [];
            $data = $product->getTradeMeData($this->fields);
            if ($this->debug) {
                echo '<h4>' . $product->Title . '</h4>';
                $data['Data'] = array_combine(array_keys($this->fields), $data['Data']);
                echo '<pre>' . print_r($data, 1) . '</pre>';
            }
            foreach ($data['Data'] as $value) {
                $innerArray[] = CsvFunctionality::removeBadCharacters($value);
            }
            if (! empty($data['Include'])) {
                if (empty($categoryListings[$data['TradeMeCategory']])) {
                    $categoryListings[$data['TradeMeCategory']] = [];
                }
                $categoryListings[$data['TradeMeCategory']][] = '<a href="/' . $product->CMSEditLink() . '">' . $product->InternalItemID . ' - ' . $product->Title . '</a>';
                ++$countForReal;
            }
            if (! empty($data['HasError'])) {
                if (! isset($data['HasError'])) {
                    $data['HasError'] = 'Unknown Error';
                }
                $this->output('Error: ' . $data['ErrorMessage'], 'bad');
            }
            $array[] = $innerArray;
        }

        $this->output(
            '
            After review, there are ' . $countForReal . ' products that will actually be listed on TradeMe,
            The rest are out of stock or have some other issue.
            Below is a list by TradeMe Category.',
            'orange'
        );

        $this->output('<h1>By Category</h1>');
        ksort($categoryListings);
        $this->output($this->arrayToHtml($categoryListings));

        return $array;
    }

    /**
     * we use this method to pre-select products that are eligible:
     * see WHERE statement for LOGIC.
     */
    protected function getIDsOfProducts(): array
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
                "ProductGroup_Live" AS ParentProductGroup ON "ParentSiteTree"."ID" = "ParentProductGroup"."ID"
            INNER JOIN
                "Product_Live" AS Product ON "SiteTree_Live"."ID" = "Product"."ID"
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
        foreach ($rows as $row) {
            $array[$row['ProductID']] = $row['ProductID'];
        }

        return $array;
    }

    protected function arrayToHtml(array $array): string
    {
        $html = '<ul>';
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $html .= '<li>' . $key . ':' . $this->arrayToHtml($value) . '</li>';
            } else {
                $html .= '<li>' . $value . '</li>';
            }
        }

        return $html . '</ul>';
    }

    protected function output(string $msg, ?string $style = '')
    {
        switch ($style) {
            case 'good':
            case 'created':
                $colour = 'green';

                break;
            case 'info':
            case 'obsolete':
                $colour = 'orange';

                break;
            case 'bad':
            case 'deleted':
                $colour = 'red';

                break;
            default:
                $colour = 'black';
        }
        $msg = '<div style="color: ' . $colour . ';">' . $msg . '</div>';
        if ($this->verbose) {
            echo $msg;
        } else {
            $this->html .= $msg;
        }
    }
}
