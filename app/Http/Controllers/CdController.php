<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\CdModel;
use App\CategoryModel;
 
class CdController extends Controller
{
    /**
     * The following method is used to get all available CD's list (can be filtered)
     * 
     * @param array $request Contains all filter specifications. The specifications are: title, category, 
     * min_price, max_price, min_quantity, and max_quantity)
     * @return array Return all available CD's (and those which satisfy the filter), which have not been soft 
     * deleted. Each Cd will provide information about id, category_id, category, title, rate, quantity, 
     * created_at, updated_at, and deleted_at)
     */
    public function get(Request $request)
    {
        /* Query all available CD's */
        $cds_list = CdModel::where("deleted_at", null);

        /*---------- Filter Proccess ----------*/
        /* By Title */
        if ($request->get("title") != null) {
            $cds_list = $cds_list->where("title", "LIKE", "%" . $request->get("title") . "%");
        }

        /* By Category */
        if ($request->get("category") != null) {
            $category = CategoryModel::where("name", $request->get("category"))->first();

            if (count($category) != 0 and $category->deleted_at == null) {
                /* Handling case when the category matched */
                $cds_list = $cds_list->where("category_id", $category->id);
            } elseif ($category->deleted_at != null) {
                /* Handling case when the category has been soft deleted */
                $cds_list = $cds_list->where("category_id", 0);
            }
        }

        /* By Price */
        if ($request->get("min_price") != null) {
            /* Searching CD which price greater than or equal to inputted value */
            $cds_list = $cds_list->where("rate", ">=", $request->get("min_price"));
        }
        if ($request->get("max_price") != null) {
            /* Searching CD which price lesser than or equal to inputted value */
            $cds_list = $cds_list->where("rate", "<=", $request->get("max_price"));
        }

        /* By Quantity */
        if ($request->get("min_quantity") != null) {
            /* Searching CD which quantity greater than or equal to inputted value */
            $cds_list = $cds_list->where("quantity", ">=", $request->get("min_quantity"));
        }
        if ($request->get("max_quantity") != null) {
            /* Searching CD which quantity lesser than or equal to inputted value */
            $cds_list = $cds_list->where("quantity", "<=", $request->get("max_quantity"));
        }

        /* Prepare and return the response */
        $cds_list = $cds_list->get();
        foreach ($cds_list as $cd) {
            /* Searching category name */
            $cd_category = CategoryModel::where("id", $cd->category_id)->first();
            $cd["category"] = $cd_category->name;
        }
        return response($cds_list, 200)->header('Content-Type', "application/json");
    }
    
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

            /* Positivity constraint */
            if ($request->input("rate") < 0 or $request->input("quantity") < 0) {
                $response["message"] = "Rate and quantity cannot take negative value";
                return response($response, 422)->header('Content-Type', "application/json");
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

    /**
     * The following method is used to edit information of a selected CD
     * 
     * @param array $request Contains: category_id, title, rate, and quantity
     * @param integer $id CD ID
     * @return string Return message: whether the proccess is success or not (if not, it will return the reason
     * why the proccess failed)
     * @return array Return cd: all information about the inserted CD (id, category, category_id, title, rate,
     * quantity, created_at, and updated_at)
     */
    public function put(Request $request, $id)
    {
        /* Check whether the request has empty field or not */
        if ($request->input("category_id") != null and $request->input("title") != null and $request->input("rate") != null and $request->input("quantity") != null) {
            /* Searching the CD */
            $cd = CdModel::where("id", $id)->where("deleted_at", null)->get();
            if (count($cd) == 0) {
                $response["message"] = "The CD you are looking for doesn't exist";
                return response($response, 404)->header('Content-Type', "application/json");
            }

            /* Check for uniqueness */
            $other_cd = CdModel::where("category_id", $request->input("category_id"))->where("title", $request->input("title"))->get();
            if (count($other_cd) != 0) {
                if (
                    $other_cd[0]->category_id != $cd[0]->category_id
                    or $other_cd[0]->title != $cd[0]->title
                ) {
                    /* Handling case for duplicate entry */
                    $response["message"] = "The CD you want to insert has already exist";
                    return response($response, 409)->header('Content-Type', "application/json");
                }
            }

            /* Check for available category */
            $related_category = CategoryModel::where("id", $request->input("category_id"))->where("deleted_at", null)->get();
            if (count($related_category) == 0) {
                $response["message"] = "Category doesn't exist";
                return response($response, 404)->header('Content-Type', "application/json");
            }

            /* Positivity constraint */
            if ($request->input("rate") < 0 or $request->input("quantity") < 0) {
                $response["message"] = "Rate and quantity cannot take negative value";
                return response($response, 422)->header('Content-Type', "application/json");
            }

            /* Edit the CD */
            $cd = $cd[0];
            $cd->category_id = $request->input("category_id");
            $cd->title = $request->input("title");
            $cd->rate = $request->input("rate");
            $cd->quantity = $request->input("quantity");
            $cd->save();
        } else {
            /* Return the reason why the proccess failed */
            $response["message"] = "All fields are required and cannot be empty";
            return response($response, 400)->header('Content-Type', "application/json");
        }

        /* Prepare and return the response */
        $response["message"] = "Success editting the CD";
        $response["cd"]["id"] = $cd->id;
        $response["cd"]["category_id"] = $cd->category_id;
        $response["cd"]["category"] = $related_category[0]->name;
        $response["cd"]["title"] = $cd->title;
        $response["cd"]["rate"] = $cd->rate;
        $response["cd"]["quantity"] = $cd->quantity;
        $response["cd"]["created_at"] = $cd->created_at;
        $response["cd"]["updated_at"] = $cd->updated_at;
        return response($response, 200)->header('Content-Type', "application/json");
    }

    /**
     * The following method is used to soft delete a CD
     * 
     * @param integer $id CD ID
     * @return string Return message: whether the proccess is success or not (if not, it will return the reason
     * why the proccess failed)
     * @return array Return cd: all information about the deleted CD (id, category_id, category, title, rate, 
     * quantity, created_at, and deleted_at)
     */
    public function softDelete($id)
    {
        /* Searching for specified CD */
        $related_cd = CdModel::where("id", $id)->first();
        if (count($related_cd) == 0 or $related_cd->deleted_at != null) {
            /* CD doesn't exist or has been soft deleted */
            $response["message"] = "The CD you are looking for doesn't exist";
            return response($response, 404)->header('Content-Type', "application/json");
        } else {
            /* Update the database*/
            $related_cd->deleted_at = date("Y-m-d H:i:s");
            $related_cd->save();

            /* Find category name */
            $related_category = CategoryModel::where("id", $related_cd->category_id)->first();
        }

        /* Prepare and return the response */
        $response["message"] = "Success deleting CD";
        $response["cd"]["id"] = $related_cd->id;
        $response["cd"]["category_id"] = $related_cd->category_id;
        $response["cd"]["category"] = $related_category->name;
        $response["cd"]["title"] = $related_cd->title;
        $response["cd"]["rate"] = $related_cd->rate;
        $response["cd"]["quantity"] = $related_cd->quantity;
        $response["cd"]["created_at"] = $related_cd->created_at;
        $response["cd"]["deleted_at"] = $related_cd->deleted_at;
        return response($response, 200)->header('Content-Type', "application/json");
    }

    /**
     * The following method is used to get CD information based on CD ID
     * 
     * @param integer $id CD ID
     * @return string Return message: only if the CD not found
     * @return array Return all information about the CD (id, category_id, category, title, rate, 
     * quantity, created_at, updated_at, and deleted_at)
     */
    public function getById($id)
    {
        /* Searching for specified CD */
        $related_cd = CdModel::where("id", $id)->first();
        if (count($related_cd) == 0 or $related_cd->deleted_at != null) {
            /* CD doesn't exist or has been soft deleted */
            $response["message"] = "The CD you are looking for doesn't exist";
            return response($response, 404)->header('Content-Type', "application/json");
        }

        /* Find category name */
        $related_category = CategoryModel::where("id", $related_cd->category_id)->first();

        /* Prepare and return the response */
        $related_cd["category"] = $related_category->name;
        return response($related_cd, 200)->header('Content-Type', "application/json");
    }
}
