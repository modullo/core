<?php


namespace App\Classes\Lms;


use App\Classes\ModulloClass;
use App\Http\Resources\Lms\ProgramsResource;
use App\Models\Lms\Programs;
use App\Models\Lms\Tenants;
use App\Models\Lms\User;
use Http\Discovery\Exception\NotFoundException;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class ProgramClass extends ModulloClass
{

    protected array $updateFields = [
        'title' => 'title',
        'description' => 'description',
        'image' => 'image',
        'video_overview' => 'video_overview',
        'visibility_type' => 'visibility_type',
        'active' => 'active',
    ];
    private User $user;
    private Tenants $tenants;
    private Programs $programs;
    public function __construct(){
        $this->user = new User;
        $this->programs = new Programs;
        $this->tenants = new Tenants;
    }


    public function fetchAllPrograms(string $search,int $limit, object $user){
        $tenant = $this->tenants->newQuery()->where('lms_user_id',$user->id)->first();
        if (!$tenant){
            throw new NotFoundException('unfortunately the tenant could not be found');
        }

        if (empty($search)) {
            $builder = $this->programs->newQuery()->where('tenant_id',$tenant->id)
                ->latest()->paginate($limit);
        } else {
            $builder = $this->programs->newQuery()->where('tenant_id',$tenant->id)
                ->when($search, function ($query) use (&$search) {
                    $query
                        ->where('title', 'like', '%' . $search . '%')
                        ->orwhere('description', 'like', '%' . $search . '%');
                })
                ->oldest('created_at')
                ->paginate($limit);
        }

        $resource =  ProgramsResource::collection($builder);
        return response()->fetch('all programs fetched successfully', $resource, 'programs');
    }

    public function createProgram(array $data, object $user){
        $program = null;
        $tenant = $this->tenants->newQuery()->where('lms_user_id',$user->id)->first();
        if (!$tenant){
            throw new NotFoundException('unfortunately the tenant could not be found');
        }
        ['title'=> $title,'description' =>$description,'visibility_type' =>$visibility_type,'image' => $image] = $data;
        DB::transaction(function () use ($title,$description,$visibility_type, &$program, $tenant,$image) {
            $program = $this->programs->newQuery()->create([
                "tenant_id" => $tenant->id,
                'title' => $title,
                'description' => $description,
                'image' => $image,
                'visibility_type' => $visibility_type,
            ]);
            $program = $this->programs->newQuery()->where('id', $program->id)->first();
            //store program subscription
            $program = new ProgramsResource($program);
        });

        if ($program === null) throw new \RuntimeException('something went wrong.. unable to create program ');
        return response()->created('program created successfully', $program,'program');

    }

    public function updateProgram(array $data, string $programId)
    {
        $program = $this->programs->newQuery()->where('uuid', $programId)->first();
        if ($program === null) {
            throw new NotFoundResourceException('unfortunately we could not find the given program');
        }
        $this->updateModelAttributes($program, $data);

        $program->save();

        $program = new ProgramsResource($program);
        return response()->updated('program updated successfully', $program, 'program');
    }

    public function getSingleProgram(string $programId)
    {
        $builder = $this->programs->newQuery()->where('uuid', $programId);
        $program = $builder->first();
        if ($program === null) {
            throw new NotFoundResourceException('unfortunately we could not find the given program');
        }
        $program = new ProgramsResource($program);
        return response()->fetch('Program fetched successfully', $program, 'program');
    }


}