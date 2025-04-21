<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\Dashboard;
use Firebase\JWT\JWT;

class DashboardController extends Controller
{
    private function generateMetabaseDashboardUrl($dashboard, $params) {
        $metabaseSiteUrl =env('METABASE_URL');
        $metabaseSecretKey = env('METABASE_SECRET_KEY');
        $decoded_params = [];
        if ($params) {
            $decoded_params = json_decode($params);
        }

        // Prepare payload
        $payload = [
            "resource" => ["dashboard" => $dashboard],
            "params" => (object)$decoded_params,
            "exp" => now()->addHours(1)->timestamp,
        ];

        // Generate JWT token
        $token = JWT::encode($payload, $metabaseSecretKey, 'HS256');

        // Build iframe URL
        $dashboard_url = "{$metabaseSiteUrl}/embed/dashboard/{$token}#bordered=true&titled=true";
        return $dashboard_url;
    }
    /**
     * Get list of dashboards as a tree
     */
    public function getDashboardTree()
    {
        try {
            $dashboards = Dashboard::orderBy('dashboard_type')
                ->orderBy('parent_id')
                ->orderBy('sort')
                ->orderBy('updated_at')
                ->get()
                ->groupBy('dashboard_type') // Group by dashboard_type
                ->map(function ($group) {
                    // Convert the grouped result to an array
                    $group = $group->values();

                    // Create a map of id => dashboard for quick lookup
                    $dashboardMap = $group->keyBy('id');

                    // Initialize the result array
                    $formattedGroup = [];

                    // Iterate over each dashboard and assign children if needed
                    foreach ($group as $dashboard) {
                        if ($dashboard->parent_id === null) {
                            // If it's a parent, find its children
                            $children = $group->filter(function ($item) use ($dashboard) {
                                return $item->parent_id === $dashboard->id;
                            });

                            // Attach children to the parent
                            $dashboard->children = $children->values();

                            // Add the parent to the result array
                            $formattedGroup[] = $dashboard;
                        }
                    }
                    return $formattedGroup; // Return the formatted result
                })
                ->toArray(); // Convert to array to ensure it's not an object

            // Output the result
            return response()->json($dashboards);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Get list of parent dashboards, dashboards without children
     */
    public function getParentDashboards()
    {
        try {
            $dashboards = Dashboard::whereNull('parent_id')
                ->orderBy('dashboard_type')
                ->orderBy('sort')
                ->get();

            return response()->json($dashboards);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getChildDashboards()
    {
        try {
            $dashboards = Dashboard::whereNotNull('parent_id')
                ->orderBy('dashboard_type')
                ->orderBy('sort')
                ->get();

            return response()->json($dashboards);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate(
                [
                    'dashboard_type' => 'required|string|max:255',
                    'title' => 'required|string|max:255',
                    'description' => 'nullable|string|max:1000',
                    'is_draggable' => 'required|boolean',
                    'is_resizable' => 'boolean',
                    'is_bounded' => 'boolean',
                    'icon' => 'nullable|string|max:255',
                    'parent_id' => 'exists:dashboards,id',
                    'dashboard' => 'nullable|integer|min:0',
                    'sort'=> 'nullable|integer'
                ],
                [
                    'dashboard_type.required' => 'ត្រូវការបំពេញប្រភេទទម្រង់ (dashboard_type)',
                    'title.required' => 'ត្រូវការចំណងជើងរបស់ទម្រង់ (title)',
                    'parent_id.exists' => 'ទម្រង់បង្ហាញមិនមានក្នុងប្រព័ន្ធទេ! (parent_id)'
                ]
            );

            $dashboard = Dashboard::create($validatedData);

            return response()->json([
                'message' => 'បានបញ្ចូលទម្រង់ដែលមានការបំពេញដោយជោគជ័យ',
                'dashboard' => $dashboard
            ]);
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
            $dashboard = Dashboard::with('widgets')->findOrFail($id);
            if ($dashboard->dashboard) {
                $metabaseSiteUrl =env('METABASE_URL');
                $dashboard->dashboard_url = $this->generateMetabaseDashboardUrl($dashboard->dashboard, $dashboard->params);
                $dashboard->dashboard_edit_url = "{$metabaseSiteUrl}/dashboard/{$dashboard->dashboard}";
            }
            return response()->json($dashboard);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានទម្រង់នេះទេ!'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validatedData = $request->validate(
                [
                    'dashboard_type' => 'sometimes|string|max:255',
                    'title' => 'sometimes|string|max:255',
                    'description' => 'nullable|string|max:1000',
                    'is_draggable' => 'sometimes|boolean',
                    'is_resizable' => 'boolean',
                    'is_bounded' => 'boolean',
                    'icon' => 'nullable|string|max:255',
                    'parent_id' => 'exists:dashboards,id',
                    'dashboard' => 'nullable|integer|min:0',
                    'sort'=> 'nullable|integer'
                ]
            );

            $dashboard = Dashboard::findOrFail($id);
            $dashboard->update($validatedData);
            $dashboard->refresh();

            return response()->json([
                'message' => 'កែប្រែជោគជ័យ',
                'dashboard' => $dashboard
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានទម្រង់នេះទេ!'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $dashboard = Dashboard::findOrFail($id);

            // if ($dashboard->widgets->count() > 0) {
            //     return response()->json(['error' => 'មានទម្រង់នេះមានរបស់ទម្រង់ទេ ក៏មិនអាចលុបទេ!'], 400);
            // }

            $dashboard->delete();

            return response()->json([
                'message' => 'បានលុបជោគជ័យ',
                'dashboard' => $dashboard
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានទម្រង់នេះទេ!'], 404);
        }
    }
}
