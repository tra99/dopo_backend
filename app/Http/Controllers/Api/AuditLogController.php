<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Share\Converter;
use Carbon\Carbon;


class AuditLogController extends Controller
{
    protected $converter;

    public function __construct(
        Converter $converter
    ) {
        $this->converter = $converter;
    }
    public function index(Request $request)
    {
        try {
            $byUserId = $request->input('by_user_id');
            $byEvent = $request->input('by_event');
            $byStartDate = $request->input('start_date');
            $byEndDate = $request->input('end_date');

            $sortBy = $request->input('sort_by', 'created_at');
            $sortDirection = $request->input('sort_direction', 'desc');

            $perPage = $request->input('limit');


            $auditLogs = AuditLog::with('user')
                ->when($byUserId, function ($query) use ($byUserId) {
                    return $query->where('causer_id', $byUserId);
                })->when($byEvent, function ($query) use ($byEvent) {
                    return $query->where('event', $byEvent);
                })->when($byStartDate, function ($query) use ($byStartDate) {
                    return $query->whereDate('created_at', '>=', $byStartDate);
                })->when($byEndDate, function ($query) use ($byEndDate) {
                    return $query->whereDate('created_at', '<=', $byEndDate);
                })->when($sortBy, function ($query) use ($sortBy, $sortDirection) {
                    return $query->orderBy($sortBy, $sortDirection);
                })->paginate($perPage);

            return response()->json($auditLogs);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $auditLog = AuditLog::with('user')->findOrFail($id);
            return response()->json($auditLog);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានសម្មកម្មភាពនេះទេ!'], 404);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $auditLog = AuditLog::findOrFail($id);
            $auditLog->delete();

            return response()->json([
                'message' => 'លុបជោគជ័យ',
                $auditLog
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានសម្មកម្មភាពនេះទេ!'], 404);
        }
    }

    public function logSchuduler(Request $request)
    {
        try {
            $validated = $request->validate([
                'day' => 'required|integer',
            ]);

            $auditLog = Config::where('name', 'audit log scheduler')->first();

            if (empty($auditLog)) {
                $auditLog = Config::create([
                    'name'    => 'audit log scheduler',
                    'options' => json_encode([
                        'key'   => 'days',
                        'value' => $validated['day']
                    ])
                ]);
            } else {
                $auditLog->options = json_encode([
                    'key'   => 'days',
                    'value' => $validated['day']
                ]);
                $auditLog->save();
            }

            return response()->json([
                'message' => 'កំណត់ជោគជ័យ',
                'data'    => $auditLog,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // download log
    public function exportCvs(Request $request)
    {
        try {
            $byUserId = $request->input('by_user_id');
            $byEvent = $request->input('by_event');
            $byStartDate = $request->input('start_date');
            $byEndDate = $request->input('end_date');
            $sortBy = $request->input('sort_by', 'created_at');
            $sortDirection = $request->input('sort_direction', 'desc');
            $auditLogs = AuditLog::with('user')->get();


            $query = AuditLog::with('user') // Ensure you are loading the 'user' relationship
                ->when($byUserId, function ($query) use ($byUserId) {
                    return $query->where('causer_id', $byUserId);
                })
                ->when($byEvent, function ($query) use ($byEvent) {
                    return $query->where('event', $byEvent);
                })
                ->when($byStartDate, function ($query) use ($byStartDate) {
                    return $query->whereDate('created_at', '>=', $byStartDate);
                })
                ->when($byEndDate, function ($query) use ($byEndDate) {
                    return $query->whereDate('created_at', '<=', $byEndDate);
                })
                ->when($sortBy, function ($query) use ($sortBy, $sortDirection) {
                    return $query->orderBy($sortBy, $sortDirection);
                });

            $results = $query->get();
            $results = $results->map(function ($item) {
                // Decode attributes if it's not null or empty
                $item['user_agent'] = $item['properties']['user_agent'];
                $item['ip_address'] = $item['properties']['ip_address'];

                unset($item['properties']);
                unset($item['causer_id']);
                return $item;
            });

            // Generating the Excel file and saving it temporarily
            $data = now()->format('Y-m-d');
            $fileName = 'audit_logs_' . $data . '.csv';


            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No', 'log name', 'event', 'data', 'user', 'ip address', 'user agent', 'date', 'new change', 'old data', 'log id']);

            $no = 1;
            foreach ($results as $result) {
                $data = explode('\\', $result->subject_type)[2];
                // dd($data);
                fputcsv($handle, [
                    $no,
                    $result->log_name,
                    $result->event,
                    $this->converter->translateModelName($data),
                    $result->user['name'] ?? 'មិនបានបញ្ជាក់',
                    $result->user_agent,
                    $this->converter->convertToKhmerTimezone($result->created_at)->format('d-m-Y H:i:s'),
                    '',
                    '',
                    $result->id,
                ]);
                $no++;
            }
            // fclose($handle);

            // Set the proper headers for the CSV download
            return response()->stream(function () use ($handle) {
                fclose($handle);
            }, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
