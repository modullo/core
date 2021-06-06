<?php


namespace App\Http\Controllers\Lms;


use App\Classes\Lms\TenantClass;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TenantsController extends Controller
{
    private TenantClass $tenantClass;

    public function __construct()
    {
        $this->tenantClass = new TenantClass;
    }

    /**
     * @throws ValidationException
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:lms_users',
            'password' => 'required',
            'country' => 'nullable|string',
            'company_name' => 'required|string',
        ]);
        return $this->tenantClass->createTenant($request->all());
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'country' => 'nullable|string',
            'company_name' => 'nullable|string',
        ]);
        return $this->tenantClass->updateTenant($request->tenantId,$request->all());
    }


    public function single(Request $request)
    {
        return $this->tenantClass->singleTenant($request->tenantId);
    }
}