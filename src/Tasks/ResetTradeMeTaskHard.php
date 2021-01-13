<?php

namespace Sunnysideup\EcommerceTrademe\Tasks;

use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DB;

/**
 * create CSV for TradeMe
 */
class ResetTradeMeTaskHard extends BuildTask
{
    /**
     * @var bool
     */
    protected $enabled = false;

    /**
     * @inherit
     */
    protected $title = 'Reset All TradeMe Details - Hard Reset';

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
        foreach (['', '_Live'] as $extension) {
            DB::query('Update "Product' . $extension . '" SET ShowOnTradeMe = \'follow category\';');
        }
    }
}
