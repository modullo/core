<?php


namespace App\Classes\Lms;


use App\Classes\ModulloClass;
use App\Http\Resources\Lms\ProgramsResource;
use App\Models\Lms\Programs;
use App\Models\Lms\Tenants;
use App\Models\Lms\User;
use Http\Discovery\Exception\NotFoundException;
use Illuminate\Support\Facades\DB;

class ProgramClass extends ModulloClass
{
    private User $user;
    private Tenants $tenants;
    private Programs $programs;
    public function __construct(){
        $this->user = new User;
        $this->programs = new Programs;
        $this->tenants = new Tenants;
    }

    public function createProgram(array $data, object $user){
        $program = null;
        $tenant = $this->tenants->newQuery()->where('lms_user_id',$user->id)->first();
        if (!$tenant){
            throw new NotFoundException('unfortunately the tenant could not be found');
        }
        ['title'=> $title,'description' =>$description,'type' =>$type,'image' => $image] = $data;
        DB::transaction(function () use ($title,$description,$type, &$program, $tenant,$image) {
            $program = $this->programs->newQuery()->create([
                "tenant_id" => $tenant->id,
                'title' => $title,
                'description' => $description,
                'image' => $image,
                'type' => $type,
            ]);
            $program = $this->programs->where('id', $program->id)->first();
            //store program subscription
            $program = new ProgramsResource($program);
        });

        if ($program === null) throw new \RuntimeException('something went wrong.. unable to create program ');
        return response()->created('program created successfully', $program,'program');

    }

}