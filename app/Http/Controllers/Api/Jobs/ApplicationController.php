<?php

namespace App\Http\Controllers\Api\Jobs;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Applicant;
use App\Models\Job;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    //
    public function createApplication(Request $request)
    {
        $request->validate([
            'applicant_id' => 'required',
            'job_id' => 'required',
        ]);

        $job = Job::where('id', $request->job_id)->first();

        if ($job->alotted_capacity === 0) {
            return response()->json(
                [
                    'message' => 'Job capacity is full',
                ],
                401
            );
        }

        $existingApplication = Applicant::where(
            'applicant_id',
            $request->applicant_id
        )
            ->where('job_id', $request->job_id)
            ->first();

        if ($existingApplication) {
            return response()->json(
                [
                    'message' => 'You have already applied for this job',
                ],
                401
            );
        }

        $applicant = Applicant::create([
            'applicant_id' => $request->applicant_id,
            'job_id' => $request->job_id,
        ]);

        $job->alotted_capacity = $job->alotted_capacity - 1;
        $job->save();

        return response()->json(
            [
                'message' =>
                    'You have successfully applied for this job. Please wait for the employer to accept your application.',
                'data' => $applicant,
            ],
            200
        );
    }

    public function getAllApplication(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->is_employee) {
            $job = Job::where('employer_id', $id)->first();
            $applicants = Applicant::where('job_id', $job->id)
                ->with('user')
                ->get();
            return response()->json($applicants);
        } else {
            $applicants = Applicant::where('applicant_id', $id)
                ->join('jobs', 'jobs.id', '=', 'applicants.job_id')
                ->join('companies', 'companies.id', '=', 'jobs.company_id')

                ->get();
            return response()->json([
                'success' => true,
                'data' => $applicants,
            ]);
        }
    }

    public function acceptApplication(int $id, Request $request)
    {
        $applicant = Applicant::findOrFail($id);

        if ($request->method === 'accept') {
            $applicant->is_accepted = true;
        } else {
            $applicant->is_accepted = false;
        }

        $applicant->save();

        return response()->json([
            'message' => 'Action taken successfully',
            'data' => $applicant,
        ]);
    }

    public function cancelApplication(int $id)
    {
        $applicant = Applicant::where('applicant_id', $id)->first();

        $job = Job::where('id', $applicant->job_id)->first();

        $job->alotted_capacity = $job->alotted_capacity + 1;
        $job->save();

        $applicant->delete();

        return response()->json([
            'message' => 'Application cancelled successfully',
        ]);
    }
}
