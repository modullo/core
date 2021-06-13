<?php


namespace App\Classes\Lms\Resource;


use App\Classes\ModulloClass;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Resources\Lms\AssetResource;
use App\Models\Lms\Assets;
use App\Models\Lms\Tenants;
use Exception;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Throwable;

class AssetsClass extends ModulloClass
{

   protected array $updateFields = [
     'asset_name' => 'asset_name',
     'asset_url' => 'asset_url',
     'type' => 'type'
   ];
    protected array $videoType = ["mp4","mkv","mov","3gp"];
    protected array $audioType = ["mp3","ogg"];
    protected array $imageType = ["jpeg","jpg","png","gif"];

    protected Assets $assets;
    protected Tenants $tenants;
    public function __construct(){
        $this->assets = new Assets;
        $this->tenants = new Tenants;
    }


    /**
     * @throws Exception
     */
    public function createAsset(object $user, string $asset_name, string $asset_url, string $type){
        try {
            $type = $this->deterMineFileType(strtolower($type));
            $tenant = $this->tenants->newQuery()->where('lms_user_id', $user->id)->first();
            if (!$tenant) {
                throw new ResourceNotFoundException('unfortunately the tenant could not found');
            }
            $asset  =  $this->assets->newQuery()->create([
                "tenant_id" => $tenant->id,
                "asset_name" => $asset_name,
                "asset_url" => $asset_url,
                "type" => $type
            ]);
            $resource = new AssetResource($asset);
            return response()->created('asset created successfully',$resource,'asset');

        } catch (Exception $th) {
            throw new Exception($th->getMessage());
            //send to sentry and return graceful error
        }
    }

    public function updateAsset(string $assetId,array $data)
    {

        $type = $this->deterMineFileType(strtolower($data['type']));
        $data['type'] = $type;
        $asset  =  $this->assets->newQuery()->where("uuid",$assetId)->first();
        if(!$asset)
        {
            throw new ResourceNotFoundException("Asset not found");
        }
        $this->updateModelAttributes($asset,$data);
        $asset->save();
        $resource = new AssetResource($asset);
        return response()->updated("Asset updated successfully",$resource,"asset");

    }



    public function showAsset(string $assetId)
    {

        $asset  =  $this->assets->newQuery()->where("uuid",$assetId)->first();
        if(!$asset)
        {
            throw new ResourceNotFoundException("Asset not found");
        }
        $resource = new AssetResource($asset);
        return response()->fetch("Asset fetched successfully",$resource,"asset");

    }
    public function fetchAssets(object $user)
    {
        $tenant = $this->tenants->newQuery()->where('lms_user_id', $user->id)->first();
        if (!$tenant) {
            throw new ResourceNotFoundException('unfortunately the tenant could not found');
        }
        $assets =  $this->assets->newQuery()->where('tenant_id',$tenant->id)->get();
        $resource = AssetResource::collection($assets);
        return response()->fetch("Assets fetched successfully",$resource,"assets");
    }

    private function deterMineFileType($type): string
    {

        switch (true) {
            case in_array($type,$this->videoType):
                # code...
                return "video";
                break;
            case in_array($type,$this->audioType):
                # code...
                return "audio";
                break;
            case in_array($type,$this->imageType):
                # code...
                return "image";
                break;

            default:
                # code...
                return "unknown";
                break;
        }
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