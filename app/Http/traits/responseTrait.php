<?php
namespace App\Http\Traits;
trait ResponseTrait {
    public function ValidationErrorsResponse($validation) {
            $errors = $validation->errors();
            return response()->json([
                'status'     => false,
                'message'    => 'Validation Error',
                'errors'     => $errors
            ]);
    }

    public function returnResponse($status, $message, $data = null)
    {
        if($data != null){
            return response()->json([
                'status'   => $status,
                'message'  => $message,
                'data'     => $data
            ]);
        }

        if($data == null){
            return response()->json([
                'status'   => $status,
                'message'  => $message
            ]);
        }
    }

}
