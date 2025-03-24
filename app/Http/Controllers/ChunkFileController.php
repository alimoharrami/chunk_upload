<?php

namespace App\Http\Controllers;

use App\Services\UploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ChunkFileController extends Controller
{
    function __construct(
        private readonly UploadService $uploadService,
    )
    {
        //
    }

    function __invoke(Request $request): JsonResponse
    {
        if($request->file('chunkfile')){
            $folderName = $this->uploadService->simpleUpload($request->file('chunkfile'));
            if($folderName) return response()->json($folderName, ResponseAlias::HTTP_OK);
            return response()->json('error', ResponseAlias::HTTP_NOT_ACCEPTABLE);
        }elseif( $request->method() == 'POST')
        {
            $folderName = $this->uploadService->startChunkProcess();
            if($folderName) return response()->json($folderName, ResponseAlias::HTTP_OK);
            return response()->json('error', ResponseAlias::HTTP_NOT_ACCEPTABLE);
        }else{
            $result = $this->uploadService->processChunkUploads($request);
            if(!$result) return response()->json('error', ResponseAlias::HTTP_NOT_ACCEPTABLE);
        }

        return response()->json('success', ResponseAlias::HTTP_OK);
    }
}
