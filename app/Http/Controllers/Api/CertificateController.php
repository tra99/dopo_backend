<?php
namespace App\Http\Controllers\Api;

use App\Models\Certificate;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index()
    {
        return Certificate::with(['user', 'course'])->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'            => 'required|exists:users,id',
            'course_id'          => 'required|exists:course,id',
            'date_of_certificate'=> 'required|date',
        ]);

        $cert = Certificate::create($data);

        return response()->json($cert, 201);
    }

    public function show(Certificate $certificate)
    {
        return $certificate->load('user', 'course');
    }

    public function update(Request $request, Certificate $certificate)
    {
        $data = $request->validate([
            'date_of_certificate'=> 'sometimes|required|date',
        ]);

        $certificate->update($data);

        return response()->json($certificate);
    }

    public function destroy(Certificate $certificate)
    {
        $certificate->delete();
        return response()->noContent();
    }
}
