<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\ContributionServiceInterface;
use Illuminate\Http\Request;

class ContributionController extends Controller
{
    private ContributionServiceInterface $contributionService;

    public function __construct(ContributionServiceInterface $contributionService)
    {
        $this->contributionService = $contributionService;
    }
}
