<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\CategoryModel;
 
class CategoryController extends Controller
{
    /**
     * The following method is used to get all available categories list
     * 
     * @return array Return all available categories, which have not been soft deleted (each category will
     * give information about id, name, created_at, updated_at, and deleted_at)
     */
    public function get()
    {
        /* Query all categories */
        $categories_list = CategoryModel::all();

        /* Filter available categories only (have not been deleted) */
        $available_categories = array();
        foreach ($categories_list as $category) {
            if (!$category->deleted_at) {
                array_push($available_categories, $category);
            }
        }

        return response($available_categories, 200)->header('Content-Type', "application/json");
    }

    /**
     * The following method is used to add new category
     * 
     * @param array $request Contains name key
     * @return string Return message: whether the proccess is success or not (if not, it will return the reason
     * why the proccess failed)
     * @return array Return category: all information about the added category (id, name, and created_at)
     */
    public function post(Request $request)
    {
        /* Check whether the request has empty field or not */
        if ($request->input("name")) {
            /* Check for uniqueness */
            $related_category = CategoryModel::where("name", $request->input("name"))->get();
            if (count($related_category) != 0) {
                /* Case when the category has already exist */
                $response["message"] = "This category name has already exist";
                return response($response, 409)->header('Content-Type', "application/json");
            }

            /* Add new category to database */
            $new_category = new CategoryModel();
            $new_category->name = $request->input("name");
            $new_category->save();
        } else {
            /* Return the reason why the proccess failed */
            $response["message"] = "Name field is required";
            return response($response, 400)->header('Content-Type', "application/json");
        }

        /* Prepare and return the response */
        $response["message"] = "Success adding new category";
        $response["category"]["id"] = $new_category->id;
        $response["category"]["name"] = $new_category->name;
        $response["category"]["created_at"] = $new_category->created_at;
        return response($response, 200)->header('Content-Type', "application/json");
    }

    /**
     * The following method is used to change a category name
     * 
     * @param array $request Contains name key
     * @param integer $id Category ID
     * @return string Return message: whether the proccess is success or not (if not, it will return the reason
     * why the proccess failed)
     * @return array Return category: all information about the updated category (id, name, and created_at, and
     * updated_at)
     */
    public function put(Request $request, $id)
    {
        /* Check whether the request has empty field or not */
        if ($request->input("name")) {
            /* Check whether specified category exist or not */
            $related_category = CategoryModel::where("id", $id)->first();
            if (count($related_category) == 0 or $related_category->deleted_at != null) {
                $response["message"] = "The category you are looking for doesn't exist";
                return response($response, 404)->header('Content-Type', "application/json");
            } else {
                /* Update the database */
                $related_category->name = $request->input("name");
                $related_category->save();
            }

        } else {
            /* Return the reason why the proccess failed */
            $response["message"] = "Name field is required";
            return response($response, 400)->header('Content-Type', "application/json");
        }

        /* Prepare and return the response */
        $response["message"] = "Success editting category";
        $response["category"]["id"] = $related_category->id;
        $response["category"]["name"] = $related_category->name;
        $response["category"]["created_at"] = $related_category->created_at;
        $response["category"]["updated_at"] = $related_category->updated_at;
        return response($response, 200)->header('Content-Type', "application/json");
    }

    /**
     * The following method is used to soft delete a category
     * 
     * @param integer $id Category ID
     * @return string Return message: whether the proccess is success or not (if not, it will return the reason
     * why the proccess failed)
     * @return array Return category: all information about the deleted category (id, name, and created_at, and
     * deleted_at)
     */
    public function softDelete($id)
    {
        /* Searching for specified category */
        $related_category = CategoryModel::where("id", $id)->first();
        if (count($related_category) == 0 or $related_category->deleted_at != null) {
            /* Category doesn't exist or has been soft deleted */
            $response["message"] = "The category you are looking for doesn't exist";
            return response($response, 404)->header('Content-Type', "application/json");
        } else {
            /* Update the database*/
            $related_category->deleted_at = date("Y-m-d H:i:s");
            $related_category->save();
        }

        /* Prepare and return the response */
        $response["message"] = "Success deleting category";
        $response["category"]["id"] = $related_category->id;
        $response["category"]["name"] = $related_category->name;
        $response["category"]["created_at"] = $related_category->created_at;
        $response["category"]["deleted_at"] = $related_category->deleted_at;
        return response($response, 200)->header('Content-Type', "application/json");
    }
}
