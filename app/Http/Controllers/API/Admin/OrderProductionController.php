<?php

namespace App\Http\Controllers\API\Admin;


use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\OrderProductionStage;


class OrderProductionController extends Controller
{


    /**
     * Show production history
     */
    public function history(Order $order)
    {


        return response()->json(

            $order
            ->productionStageHistory()
            ->with('stage')
            ->get()

        );


    }




    /**
     * Move order to next stage
     */
    public function changeStage(
        Order $order
    )
    {


        return DB::transaction(function()
        use($order){


            $currentStage =
                $order->currentProductionStage;



            $nextStage =
                $currentStage
                ?
                $currentStage
                ->next()
                :
                OrderProductionStage::orderBy('sort_order')->first();



            if(!$nextStage)
            {

                return response()->json([

                    'message'=>
                    'No next production stage'

                ]);

            }




            $order->productionStageHistory()
            ->create([

                'stage_id'
                =>
                $nextStage->id,

            ]);


            $order->update([
                'current_production_stage_id' => $nextStage->id,
            ]);




            return response()->json([


                'message'=>
                'Order moved to next stage',


                'stage'=>
                $nextStage->name


            ]);


        });


    }





    /**
     * Set specific production stage
     */
    public function updateStage(
        Order $order,
        $stageId
    )
    {


        $order
        ->productionStageHistory()
        ->create([

            'stage_id'
            =>
            $stageId,
        ]);

        $order->update([
        'current_production_stage_id'=>$stageId
        ]);

        return response()->json([

            'message'=>
            'Production stage updated'

        ]);

    }



}