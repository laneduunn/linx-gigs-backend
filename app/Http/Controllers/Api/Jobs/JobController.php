<?php

namespace App\Http\Controllers\Api\Jobs;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    //
    public function createJob(Request $request)
    {
        $request->validate([
            'job_title' => 'required|string',
            'job_description' => 'required|string',
            'offered_position' => 'required|string',
            'qualifications' => 'required|string',
            'alloted_capacity' => 'required|integer',
            'salary' => 'required|integer',
        ]);

        $job = Job::create([
            'job_title' => $request->job_title,
            'job_description' => $request->job_description,
            'offered_position' => $request->offered_position,
            'qualifications' => $request->qualifications,
            'alotted_capacity' => $request->alloted_capacity,
            'salary' => $request->salary,
            'employer_id' => $request->employer_id,
            'company_id' => $request->company_id,
        ]);

        return response()->json(
            [
                'message' => 'Job created successfully',
                'job' => $job,
            ],
            201
        );
    }

    public function getJobs()
    {
        $jobs = Job::with('applicants', 'company')->get();

        if (!$jobs) {
            return response()->json(
                [
                    'message' => 'Jobs not found',
                ],
                404
            );
        }

        return response()->json(
            [
                'message' => 'Jobs found',
                'jobs' => $jobs,
            ],
            200
        );
    }

    public function searchJob(Request $request)
    {
        $job = Job::where(
            'job_title',
            'ILIKE',
            '%' . $request->job_title . '%'
        )->get();

        if (!$job) {
            return response()->json(
                [
                    'message' => 'Job not found',
                ],
                404
            );
        }

        return response()->json(
            [
                'message' => 'Job found',
                'job' => $job,
            ],
            200
        );
    }

    public function getJobsOfSpecificEmployer(int $emp_id)
    {
        $jobs = Job::where('employer_id', $emp_id)
            ->with('applicants', 'company')
            ->get();

        if (!$jobs) {
            return response()->json(
                [
                    'message' => 'Jobs not found',
                ],
                404
            );
        }

        return response()->json(
            [
                'message' => 'Jobs found',
                'jobs' => $jobs,
            ],
            200
        );
    }

    public function deleteJob(int $id)
    {
        $job = Job::findOrFail($id);

        if (!$job) {
            return response()->json(
                [
                    'message' => 'Job not found',
                ],
                404
            );
        }

        $job->delete();

        return response()->json(
            [
                'message' => 'Job deleted successfully',
            ],
            200
        );
    }
}
