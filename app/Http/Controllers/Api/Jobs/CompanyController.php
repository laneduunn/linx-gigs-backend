<?php

namespace App\Http\Controllers\Api\Jobs;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    //
    public function createCompany(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string',
            'company_address' => 'required|string',
        ]);

        $existingCompany = Company::where(
            'company_name',
            $request->company_name
        )->first();

        if ($existingCompany) {
            return response()->json(
                [
                    'message' => 'Company already exists',
                ],
                409
            );
        }

        $company = Company::create([
            'image_url' => $request->image_url,
            'company_name' => $request->company_name,
            'company_address' => $request->company_address,
            'employer_id' => $request->employer_id,
        ]);

        return response()->json(
            [
                'message' => 'Company created successfully',
                'company' => $company,
            ],
            201
        );
    }

    public function getCompany(int $id)
    {
        $user = User::where('id', $id)
            ->with('company')
            ->first();

        if (!$user) {
            return response()->json(
                [
                    'message' => 'User not found',
                ],
                404
            );
        }

        return response()->json(
            [
                'message' => 'Company retrieved successfully',
                'company' => $user->company,
            ],
            200
        );
    }

    public function deleteCompany(int $id)
    {
        $company = Company::findOrFail($id);

        $company->delete();

        return response()->json(
            [
                'message' => 'Company deleted successfully',
            ],
            200
        );
    }

    public function editCompany(int $id, Request $request)
    {
        $company = Company::findOrFail($id);

        $company->update([
            'image_url' => $request->image_url,
            'company_name' => $request->company_name,
            'company_address' => $request->company_address,
            'employer_id' => $request->employer_id,
        ]);

        return response()->json(
            [
                'message' => 'Company updated successfully',
                'company' => $company,
            ],
            200
        );
    }
}
