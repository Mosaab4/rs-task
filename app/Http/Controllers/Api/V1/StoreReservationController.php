<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Actions\StoreReservationAction;
use App\Http\Requests\StoreReservationRequest;

class StoreReservationController extends Controller
{
    public function __invoke(StoreReservationRequest $request)
    {
        $storeReservationAction = new StoreReservationAction($request);
        [$status, $message] = $storeReservationAction->execute();

        if (!$status) {
            return $this->respondWithError($message);
        }

        return $this->respondWithSuccess(new \stdClass(),"Booked Successfully");
    }
}
