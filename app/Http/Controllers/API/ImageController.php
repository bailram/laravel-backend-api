<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\RequestResource;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{
    public function load($filename)
    {
        $path = storage_path('app/public/images/'.$filename);        

        if (!File::exists($path)) {
            return response(['error' => $path, 'File Not Found']);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
            ['user_id' => 'required', 'file' => 'required|mimes:png,jpg,jpeg|max:2048']
        );

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        if ($files = $request->file('file')) {
            // store file into document folder            
            $custom_filename = time().'-'.$request->user_id.'-'.$request->file('file')->getClientOriginalName();
            $path = $request->file('file')->storeAs('public/images', $custom_filename);

            // store file into database
            $document = new Image();
            $document->title = $custom_filename;
            $document->user_id = $request->user_id;
            $document->save();

            return response(['image' => new RequestResource($document), 'message' => 'Upload successfully'], 200);            
        }
    }

    public function delete($user_id)
    {
        // TODO: do delete image
    }
}
