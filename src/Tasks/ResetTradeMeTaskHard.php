<?php

namespace Sunnysideup\EcommerceTrademe\Tasks;

use SilverStripe\Core\Environment;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DB;

/**
 * create CSV for TradeMe.
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
     * Run.
     *
     * @param mixed $request
     */
    public function run($request)
    {
        Environment::increaseTimeLimitTo(600);
        foreach (['', '_Live'] as $extension) {
            DB::query('Update "Product' . $extension . '" SET ShowOnTradeMe = \'follow category\';');
        }
    }
}
