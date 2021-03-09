<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CollectPaymentDetails;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Services\Interfaces\ContributionServiceInterface;
use Illuminate\Http\Request;
use Unicodeveloper\Paystack\Paystack;

class ContributionController extends Controller
{
    private ContributionServiceInterface $contributionService;

    public function __construct(ContributionServiceInterface $contributionService)
    {
        $this->contributionService = $contributionService;
    }

    //TODO: Abstract payment functionality to payyment service (pay and verify as methods)
    public function initiatePaymentProvider(CollectPaymentDetails $request, Event $event){
        $data = $request->validated();
        $paystack = new Paystack();

        //generate Payment URL
        $url = $paystack->getAuthorizationResponse($data)['data'];

        //TODO: Create Redis Cache to write data about contribution to it
        return response()->json(["url" => $url]);
    }

    public function verify(){
        $paymentDetails = (new Paystack())->getPaymentData();
        //TODO: Implement writing data from cache to database when transaction is verified
    }
}
