<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\CdModel;
use App\CategoryModel;
 
class CdController extends Controller
{
    /**
     * The following method is used to insert new CD
     * 
     * @param array $request Contains: category_id, title, rate, and quantity
     * @return string Return message: whether the proccess is success or not (if not, it will return the reason
     * why the proccess failed)
     * @return array Return cd: all information about the inserted CD (id, category, category_id, title, rate,
     * quantity, and created_at)
     */
    public function post(Request $request)
    {
        /* Check whether the request has empty field or not */
        if ($request->input("category_id") != null and $request->input("title") != null and $request->input("rate") != null and $request->input("quantity") != null) {
            /* Check for uniqueness */
            $related_cd = CdModel::where("category_id", $request->input("category_id"))->where("title", $request->input("title"))->get();
            if (count($related_cd) != 0) {
                /* Handling case for duplicate entry */
                $response["message"] = "The CD you want to insert has already exist";
                return response($response, 409)->header('Content-Type', "application/json");
            }

            /* Check for available category */
            $related_category = CategoryModel::where("id", $request->input("category_id"))->where("deleted_at", null)->get();
            if (count($related_category) == 0) {
                $response["message"] = "Category doesn't exist";
                return response($response, 404)->header('Content-Type', "application/json");
            }

            /* Add new CD to database */
            $new_cd = new CdModel();
            $new_cd->category_id = $request->input("category_id");
            $new_cd->title = $request->input("title");
            $new_cd->rate = $request->input("rate");
            $new_cd->quantity = $request->input("quantity");
            $new_cd->save();
        } else {
            /* Return the reason why the proccess failed */
            $response["message"] = "All fields are required and cannot be empty";
            return response($response, 400)->header('Content-Type', "application/json");
        }

        /* Prepare and return the response */
        $response["message"] = "Success inserting new CD";
        $response["cd"]["id"] = $new_cd->id;
        $response["cd"]["category_id"] = $new_cd->category_id;
        $response["cd"]["category"] = $related_category[0]->name;
        $response["cd"]["title"] = $new_cd->title;
        $response["cd"]["rate"] = $new_cd->rate;
        $response["cd"]["quantity"] = $new_cd->quantity;
        $response["cd"]["created_at"] = $new_cd->created_at;
        return response($response, 200)->header('Content-Type', "application/json");
    }
}
