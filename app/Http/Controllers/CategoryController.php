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
}