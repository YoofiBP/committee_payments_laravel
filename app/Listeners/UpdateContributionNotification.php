<?php

namespace App\Listeners;

use App\Events\Contributed;
use App\Services\UserServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateContributionNotification
{
    private $userService;
    /**
     * Create the event listener.
     *
     * @param UserServiceInterface $userService
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Handle the event.
     *
     * @param  Contributed  $event
     * @return void
     */
    public function handle(Contributed $event)
    {
        $this->userService->updateContribution($event->user, $event->contribution);
    }
}
