<?php


/**
 * create CSV for TradeMe
 */
class ResetTradeMeTask extends BuildTask
{
    /**
     * @var bool
     */
    protected $enabled = false;

    /**
     * @inherit
     */
    protected $title = 'Reset All Trade Me Details';

    /**
     * @inherit
     */
    protected $description = '';

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * Run
     */
    public function run($request)
    {
        increase_time_limit_to(600);
        foreach(['', '_Live'] as $extension) {
            DB::query('Update "Product'.$extension.'" SET TradeMeCategoryID = 0, AlwaysShowOnTradeMe = 0');
            DB::query('Update "ProductGroup'.$extension.'" SET TradeMeCategoryID = 0, ListProductsOnTradeMe = \'some\'');
        }
    }
}
