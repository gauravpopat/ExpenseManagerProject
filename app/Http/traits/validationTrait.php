<?php
namespace App\Http\Traits;
trait ValidationTrait {
    public function ValidationErrorsResponse($validation) {
        if ($validation->fails()) {
            $errors = $validation->errors();
            return response()->json([
                'status'     => false,
                'message'    => 'Validation Error',
                'errors'     => $errors
            ]);
        }
    }
}

?>