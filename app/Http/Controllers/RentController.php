<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\CdModel;
use App\CategoryModel;
use App\UserModel;
use App\RentModel;
use App\RentDetailModel;
 
class RentController extends Controller
{   
    /**
     * The following method is used to insert new CD
     * 
     * @param array $request Contains: user_id and rent_detail. rent_detail is an array where each element
     * contains: cd_id, total_items, and total_prices
     * @return string Return message: whether the proccess is success or not (if not, it will return the reason
     * why the proccess failed)
     * @return array Return rent: all information about the transaction. It contains id, user_id, borrowed_time,
     * total_items, total_price, and rent_detail. rent_detail is an array where each element contains: cd_id,
     * total_items, and total_price
     */
    public function startRent(Request $request)
    {
        /* Check whether the request has empty field or not */
        if (
            $request->json()->get("user_id") == null
            or $request->json()->get("user_id") == ""
            or $request->json()->get("rent_detail") == null
            or $request->json()->get("rent_detail") == ""
            or $request->json()->get("rent_detail") == []
        ) {
            /* Handling case when there is empty field */
            $response["message"] = "All fields are required and cannot be empty";
            return response($response, 400)->header('Content-Type', "application/json");
        } else {
            /*---------- Validate rent detail and calculate some values ----------*/
            /* Prepare some variables */
            $total_items = 0;
            $total_price = 0;
            foreach ($request->json()->get("rent_detail") as $detail) {
                /* Positivity constraint */
                if ($detail["total_items"] < 0 or $detail["total_price"] < 0) {
                    $response["message"] = "Total items and total price cannot take negative value";
                    return response($response, 400)->header('Content-Type', "application/json");
                }
                
                /* Check CD existence */
                $cd = CdModel::where("id", $detail["cd_id"])->where("deleted_at", null)->first();
                if (count($cd) == 0) {
                    $response["message"] = "The CD you are looking for doesn't exist";
                    return response($response, 404)->header('Content-Type', "application/json");
                }

                /* Check availability */
                if ($detail["total_items"] > $cd->quantity) {
                    $response["message"] = "Stock of CD '" . $cd->title . "' doesn't enough";
                    return response($response, 404)->header('Content-Type', "application/json");
                }

                /* Calculate total items and total price */
                $total_items += $detail["total_items"];
                $total_price += $detail["total_price"];
            }

            /* Create new rent instance */
            $new_rent = new RentModel();
            $new_rent->user_id = $request->json()->get("user_id");
            $new_rent->borrowed_time = date("Y-m-d H:i:s");
            $new_rent->returned = False;
            $new_rent->total_items = $total_items;
            $new_rent->total_price = $total_price;
            $new_rent->timestamps = False;
            $new_rent->save();

            /* Update CD quantity and create the detail rent*/
            foreach ($request->json()->get("rent_detail") as $detail) {
                /* Searching the CD and update the quantity */
                $cd = CdModel::where("id", $detail["cd_id"])->first();
                $cd->quantity -= $detail["total_items"];
                $cd->save();

                /* Create detail instance */
                $rent_detail = new RentDetailModel();
                $rent_detail->rent_id = $new_rent->id;
                $rent_detail->cd_id = $cd->id;
                $rent_detail->total_items = $detail["total_items"];
                $rent_detail->total_price = $detail["total_price"];
                $rent_detail->timestamps = False;
                $rent_detail->save();
            }
        }
        
        /* Prepare and return the response */
        $response["message"] = "Transaction success";
        $response["rent"]["id"] = $new_rent->id;
        $response["rent"]["user_id"] = $new_rent->user_id;
        $response["rent"]["borrowed_time"] = $new_rent->borrowed_time;
        $response["rent"]["total_items"] = $new_rent->total_items;
        $response["rent"]["total_price"] = $new_rent->total_price;
        $response["rent"]["rent_detail"] = $request->json()->get("rent_detail");
        return response($response, 200)->header('Content-Type', "application/json");
    }
}
