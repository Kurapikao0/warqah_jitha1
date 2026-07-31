<?php

namespace App\Http\Controllers\API\Admin;


use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;



class OrderProductionController extends Controller
{


    /**
     * Show production history
     */
    public function history(Order $order)
    {


        return response()->json(

            $order
            ->productionHistory()
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
                null;



            if(!$nextStage)
            {

                return response()->json([

                    'message'=>
                    'No next production stage'

                ]);

            }




            $order->productionHistory()
            ->create([

                'order_production_stage_id'
                =>
                $nextStage->id,


                'started_at'
                =>
                now()

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
        ->productionHistory()
        ->create([

            'order_production_stage_id'
            =>
            $stageId,

            'started_at'
            =>
            now()

        ]);



        return response()->json([

            'message'=>
            'Production stage updated'

        ]);

    }



}