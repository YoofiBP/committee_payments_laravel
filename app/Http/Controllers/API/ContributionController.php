<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CollectPaymentDetails;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Services\Interfaces\ContributionServiceInterface;
use Illuminate\Http\Request;

class ContributionController extends Controller
{
    private ContributionServiceInterface $contributionService;

    public function __construct(ContributionServiceInterface $contributionService)
    {
        $this->contributionService = $contributionService;
    }

    public function initiatePaymentProvider(CollectPaymentDetails $request, Event $event){
        return new EventResource($event);
    }
}
