<?php


namespace App\Services;

use App\Services\Traits\ImplementsCrudActions;
use App\Services\Interfaces\ContributionServiceInterface;

class EloquentContributionService implements ContributionServiceInterface
{
    use ImplementsCrudActions;

}
