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
    protected $title = 'Reset All TradeMe Details';

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
        Silverstripe\Core\Environment::increaseTimeLimitTo(600);
        foreach(['', '_Live'] as $extension) {
            DB::query('Update "Product'.$extension.'" SET ShowOnTradeMe = \'follow category\' WHERE ShowOnTradeMe !== \'always\' AND AlwaysShowOnTradeMe <> 1;');
        }
    }
}
