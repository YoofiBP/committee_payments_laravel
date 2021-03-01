<?php


namespace App\Services;

use App\Services\Traits\ImplementsCrudActions;
use Illuminate\Database\Eloquent\Model;
use App\Services\Interfaces\ContributionServiceInterface;

class EloquentContributionService implements ContributionServiceInterface
{
    use ImplementsCrudActions;

}
