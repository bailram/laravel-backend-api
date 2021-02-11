<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class DocumentController extends Controller
{
    public function load($filename)
    {
        $path = storage_path('app/public/documents/'.$filename);        

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
            ['user_id' => 'required', 'file' => 'required|mimes:doc,docx,pdf,txt|max:2048']
        );

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        if ($files = $request->file('file')) {
            // store file into document folder            
            $custom_filename = time().'-'.$request->user_id.'-'.$request->file('file')->getClientOriginalName();
            $path = $request->file('file')->storeAs('public/documents', $custom_filename);

            // store file into database
            $document = new Document();
            $document->title = $custom_filename;
            $document->user_id = $request->user_id;
            $document->save();

            return response(['document' => new DocumentResource($document), 'message' => 'Upload successfully'], 200);            
        }
    }

    public function delete($user_id)
    {
        // TODO: do delete document
    }
}
