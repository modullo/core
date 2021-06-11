<?php


namespace App\Classes\Lms;


use App\Classes\ModulloClass;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class AssetsClass extends ModulloClass
{


    public function __construct(){

    }

    public function storeToS3(Request $request){

        $asset_file = $request->file('asset_file');
        $fileName = time() . '.' . $asset_file->getClientOriginalExtension();

        $s3 = Storage::disk('s3');
        $filePath = env('S3_STORAGE_FOLDER') . '/' . $fileName;
        $s3->put($filePath, file_get_contents($asset_file), 'public');

        $file_url =  Storage::url(env('S3_STORAGE_FOLDER') . '/' . $fileName);
        return response()->created("File uploaded successfully", $file_url, "file_url");
    }

}