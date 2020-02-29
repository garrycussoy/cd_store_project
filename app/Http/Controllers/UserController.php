<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\UserModel;
 
class UserController extends Controller
{   
    /**
     * The following method is used to get all registered users list (can be filtered) who have not been
     * banned (soft delete)
     * 
     * @param array $request Contains all filter specifications. The specifications are: name, identity_type,
     * identity_number, phone_number, and address)
     * @return array Return all registered users (and those which satisfy the filter), which have not been soft 
     * deleted. Each user will provide information about id, name, identity_type, identity_number, phone_number, 
     * address, created_at, updated_at, and deleted_at)
     */
    public function get(Request $request)
    {
        /* Query all registered users */
        $users_list = UserModel::where("deleted_at", null);

        /*---------- Filter Proccess ----------*/
        /* By Name */
        if ($request->get("name") != null) {
            $users_list = $users_list->where("name", "LIKE", "%" . $request->get("name") . "%");
        }

        /* By Identity Type */
        if ($request->get("identity_type") != null) {
            $users_list = $users_list->where("identity_type", $request->get("identity_type"));
        }

        /* By Identity Number */
        if ($request->get("identity_number") != null) {
            $users_list = $users_list->where("identity_number", "LIKE", "%" . $request->get("identity_number") . "%");
        }

        /* By Phone Number */
        if ($request->get("phone_number") != null) {
            $users_list = $users_list->where("phone_number", "LIKE", "%" . $request->get("phone_number") . "%");
        }

        /* By Address */
        if ($request->get("address") != null) {
            $users_list = $users_list->where("address", "LIKE", "%" . $request->get("address") . "%");
        }

        /* Prepare and return the response */
        $users_list = $users_list->get();
        return response($users_list, 200)->header('Content-Type', "application/json");
    }

    /**
     * The following method is used to add new user
     * 
     * @param array $request Contains: name, identity_type, identity_number, phone_number, and address
     * @return string Return message: whether the proccess is success or not (if not, it will return the reason
     * why the proccess failed)
     * @return array Return user: all information about the new user (id, name, identity_type, identity_number,
     * phone_number, address and created_at)
     */
    public function post(Request $request)
    {
        /* Check whether the request has empty field or not */
        if ($request->input("name") != null 
            and $request->input("identity_type") != null 
            and $request->input("identity_number") != null 
            and $request->input("phone_number") != null 
            and $request->input("address") != null) {
            /* Check for uniqueness of phone number*/
            $related_user = UserModel::where("phone_number", $request->input("phone_number"))->where("deleted_at", null)->get();
            if (count($related_user) != 0) {
                /* Handling case for duplicate phone number */
                $response["message"] = "Phone number you entered has already used";
                return response($response, 409)->header('Content-Type', "application/json");
            }

            /* Check for uniqueness of identity*/
            $related_user = UserModel::where("identity_type", $request->input("identity_type"))->where("identity_number", $request->input("identity_number"))->where("deleted_at", null)->get();
            if (count($related_user) != 0) {
                /* Handling case for duplicate identity */
                $response["message"] = "Identity number you entered has already used";
                return response($response, 409)->header('Content-Type', "application/json");
            }

            /* Add new user to database */
            $new_user = new UserModel();
            $new_user->name = $request->input("name");
            $new_user->identity_type = $request->input("identity_type");
            $new_user->identity_number = $request->input("identity_number");
            $new_user->phone_number = $request->input("phone_number");
            $new_user->address = $request->input("address");
            $new_user->save();
        } else {
            /* Return the reason why the proccess failed */
            $response["message"] = "All fields are required and cannot be empty";
            return response($response, 400)->header('Content-Type', "application/json");
        }

        /* Prepare and return the response */
        $response["message"] = "Success adding new user";
        $response["user"]["id"] = $new_user->id;
        $response["user"]["name"] = $new_user->name;
        $response["user"]["identity_type"] = $new_user->identity_type;
        $response["user"]["identity_number"] = $new_user->identity_number;
        $response["user"]["phone_number"] = $new_user->phone_number;
        $response["user"]["address"] = $new_user->address;
        $response["user"]["created_at"] = $new_user->created_at;
        return response($response, 200)->header('Content-Type', "application/json");
    }

    /**
     * The following method is used to edit information of a user
     * 
     * @param array $request Contains: name, identity_type, identity_number, phone_number, and address
     * @param array $id User ID
     * @return string Return message: whether the proccess is success or not (if not, it will return the reason
     * why the proccess failed)
     * @return array Return user: all information about the editted user (id, name, identity_type, 
     * identity_number, phone_number, address, created_at, and updated_at)
     */
    public function put(Request $request, $id)
    {
        /* Search the selected user */
        $selected_user = UserModel::where("id", $id)->where("deleted_at", null)->first();
        if (count($selected_user) == 0) {
            /* User not found */
            $response["message"] = "The user you are looking for not found";
            return response($response, 404)->header('Content-Type', "application/json");
        }

        /* Check whether the request has empty field or not */
        if ($request->input("name") != null 
            and $request->input("identity_type") != null 
            and $request->input("identity_number") != null 
            and $request->input("phone_number") != null 
            and $request->input("address") != null) {
            /* Check for uniqueness of phone number*/
            $related_user = UserModel::where("phone_number", $request->input("phone_number"))->where("deleted_at", null)->get();
            if (count($related_user) != 0) {
                if ($related_user[0]->phone_number != $selected_user->phone_number) {
                    /* Handling case for duplicate phone number */
                    $response["message"] = "Phone number you entered has already used";
                    return response($response, 409)->header('Content-Type', "application/json");
                }
            }

            /* Check for uniqueness of identity*/
            $related_user = UserModel::where("identity_type", $request->input("identity_type"))->where("identity_number", $request->input("identity_number"))->where("deleted_at", null)->get();
            if (count($related_user) != 0) {
                if (
                    $related_user[0]->identity_number != $selected_user->identity_number
                    or $related_user[0]->identity_type != $selected_user->identity_type
                ) {
                    /* Handling case for duplicate identity */
                    $response["message"] = "Identity number you entered has already used";
                    return response($response, 409)->header('Content-Type', "application/json");
                }
            }

            /* Edit user information in the database */
            $selected_user->name = $request->input("name");
            $selected_user->identity_type = $request->input("identity_type");
            $selected_user->identity_number = $request->input("identity_number");
            $selected_user->phone_number = $request->input("phone_number");
            $selected_user->address = $request->input("address");
            $selected_user->save();
        } else {
            /* Return the reason why the proccess failed */
            $response["message"] = "All fields are required and cannot be empty";
            return response($response, 400)->header('Content-Type', "application/json");
        }

        /* Prepare and return the response */
        $response["message"] = "Success editting user's information";
        $response["user"]["id"] = $selected_user->id;
        $response["user"]["name"] = $selected_user->name;
        $response["user"]["identity_type"] = $selected_user->identity_type;
        $response["user"]["identity_number"] = $selected_user->identity_number;
        $response["user"]["phone_number"] = $selected_user->phone_number;
        $response["user"]["address"] = $selected_user->address;
        $response["user"]["created_at"] = $selected_user->created_at;
        $response["user"]["updated_at"] = $selected_user->updated_at;
        return response($response, 200)->header('Content-Type', "application/json");
    }
}
