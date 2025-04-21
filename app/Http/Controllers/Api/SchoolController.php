<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MissionSchool;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\School;

class SchoolController extends Controller
{

  /**
   * Get all schools.
   */
  public function list_schools(Request $request)
  {
    try {
      $perPage = $request->input('limit', 15);
      $search = $request->input('search');

      $sortBy = $request->input('sort_by', 'created_at');
      $sortDirection = $request->input('sort_direction', 'desc');

      $withMission = $request->input('with_mission');

      $schools = School::with('missions')
        ->when($search, function ($query) use ($search) {
          $searchLower = mb_strtolower($search);
          $query->where(function ($query) use ($searchLower) {
            $query->whereRaw('LOWER(school_code) LIKE ?', ["%{$searchLower}%"])
              ->orWhereRaw('LOWER(school_name_kh) LIKE ?', ["%{$searchLower}%"])
              ->orWhereRaw('LOWER(school_name_en) LIKE ?', ["%{$searchLower}%"]);
          });
        })
        ->when($withMission, function ($query) {
          $query->whereHas('missions');
        })
        ->orderBy($sortBy, $sortDirection)
        ->paginate($perPage)
        ->appends($request->query());

      return response()->json($schools);
    } catch (\Exception $e) {
      return response()->json(['error' => $e->getMessage()], 500);
    }
  }

  public function show(string $id)
  {
    try {
      $school = School::with([
        'missions' => function ($query): void {
          $query->orderBy('start_date', 'desc');
        },
        'missions.participants',
        'missions.schoolParticipants',
        'missions.missionSchools'
      ])
        ->findOrFail($id);

      return response()->json($school);
    } catch (ModelNotFoundException $e) {
      return response()->json(['error' => 'មិនមានសាលានេះទេ!'], 404);
    }
  }
}
