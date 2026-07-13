<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success($data=null, string $msg='Success', int $code=200, array $extra=[]): JsonResponse
    {
        return response()->json(array_merge(['success'=>true,'message'=>$msg,'data'=>$data], $extra), $code);
    }

    public static function created($data=null, string $msg='Created'): JsonResponse
    {
        return static::success($data, $msg, 201);
    }

    public static function error(string $msg='Error', int $code=400, $errors=null): JsonResponse
    {
        $r = ['success'=>false,'message'=>$msg];
        if($errors!==null) $r['errors'] = $errors;
        return response()->json($r, $code);
    }

    public static function notFound(string $msg='Not found'): JsonResponse { return static::error($msg, 404); }
    public static function unauthorized(string $msg='Unauthorized'): JsonResponse { return static::error($msg, 401); }
    public static function forbidden(string $msg='Forbidden'): JsonResponse { return static::error($msg, 403); }
    public static function validationError($errors, string $msg='Validation failed'): JsonResponse { return static::error($msg, 422, $errors); }

    public static function paginated($paginator, string $msg='Success'): JsonResponse
    {
        return response()->json([
            'success'=>true, 'message'=>$msg, 'data'=>$paginator->items(),
            'meta'=>['current_page'=>$paginator->currentPage(),'last_page'=>$paginator->lastPage(),'per_page'=>$paginator->perPage(),'total'=>$paginator->total()],
        ]);
    }
}
