<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\UserModel;
 
class UserController extends Controller
{   
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
}
