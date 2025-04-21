<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Widget;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;

class WidgetController extends Controller
{
    private function generateMetabaseQuestionUrl($question, $params) {
        $metabaseSiteUrl =env('METABASE_URL');
        $metabaseSecretKey = env('METABASE_SECRET_KEY');
        $decoded_params = [];
        if ($params) {
            $decoded_params = json_decode($params);
        }

        // Prepare payload
        $payload = [
            "resource" => ["question" => $question],
            "params" => (object)$decoded_params,
            "exp" => now()->addHours(1)->timestamp,
        ];

        // Generate JWT token
        $token = JWT::encode($payload, $metabaseSecretKey, 'HS256');

        // Build iframe URL
        $widget_url = "{$metabaseSiteUrl}/embed/question/{$token}#bordered=true&titled=true";
        return $widget_url;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $metabaseSiteUrl =env('METABASE_URL');
            $byDashboardId = $request->input('dashboard_id');
            $widgets = Widget::where('dashboard_id', $byDashboardId)
                            ->orderBy('axis_y')
                            ->orderBy('axis_x')
                            ->get();
            for ($i = 0; $i < count($widgets); $i++) {
                $widget_url = null;
                $widget_edit_url = null;
                if ($widgets[$i]->question) {
                    $widget_edit_url = "{$metabaseSiteUrl}/question/{$widgets[$i]->question}";
                    $widget_url = $this->generateMetabaseQuestionUrl($widgets[$i]->question, $widgets[$i]->params);
                }
                $widgets[$i]->widget_url = $widget_url;
                $widgets[$i]->widget_edit_url = $widget_edit_url;
            }
            return response()->json($widgets);
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
                    'dashboard_id' => 'required|exists:dashboards,id',
                    'title' => 'required|string|max:255',
                    'description' => 'nullable|string|max:1000',
                    'question' => 'nullable|integer|min:0',
                    'params' => 'nullable|string',
                    'axis_x' => 'required|integer|min:0',
                    'axis_y' => 'required|integer|min:0',
                    'width' => 'required|integer|min:1',
                    'height' => 'required|integer|min:1',
                    'sort' => 'integer',
                    'component' => 'nullable|string',
                ],
                [
                    'dashboard_id.required' => 'ត្រូវការបញ្ជាក់ទម្រង់ (dashboard_id)',
                    'dashboard_id.exists' => 'ទម្រង់នេះមិនមានក្នុងប្រព័ន្ធទេ! (dashboard_id)',
                    'title.required' => 'ត្រូវការបំពេញឈ្មោះហ្វីដជេត (title)',
                    'axis_x.required' => 'ត្រូវការបញ្ជាក់ទំហំអ័ក្ស x (axis_x)',
                    'axis_y.required' => 'ត្រូវការបញ្ជាក់ទំហំអ័ក្ស y (axis_y)',
                    'width.required' => 'ត្រូវការបញ្ជាក់ទំហំហ្វីដជេត (width)',
                    'height.required' => 'ត្រូវការបញ្ជាក់កម្ពស់ហ្វីដជេត (height)',
                    'axis_x.integer' => 'ទំហំអ័ក្ស x ត្រូវជាលេខ (axis_x)',
                    'axis_y.integer' => 'ទំហំអ័ក្ស y ត្រូវជាលេខ  (axis_y)',
                    'width.integer' => 'ទំហំហ្វីដជេត ត្រូវជាលេខ (width)',
                    'height.integer' => 'កម្ពស់ហ្វីដជេត ត្រូវជាលេខ (height)',
                    'axis_x.min' => 'ទំហំអ័ក្ស x មិនត្រូវតូចជាង ០ (axis_x)',
                    'axis_y.min' => '​ទំហំអ័ក្ស y មិនត្រូវតូចជាង ០ (axis_y)',
                    'width.min' => 'ទំហំហ្វីដជេត មិនត្រូវតូចជាង ១ (width)',
                    'height.min' => 'កម្ពស់ហ្វីដជេត មិនត្រូវតូចជាង ១ (height)',
                    'question.min' => 'លេខសំណួររបស់ភ្ជាប់ហ្វីដជេតមិនត្រឹមត្រូវទេ',
                    'description.max' => 'ពិពណ៌នា ត្រូវធំជាង ១០០០ (description)',
                    'description.string' => 'ពិពណ៌នា ជាអក្សរ(description)',
                ]
            );


            $widget = Widget::create($validatedData);
            return response()->json([
                'message' => 'បានបញ្ចូលហ្វីដជេតដែលជាទំព័ររបស់អ្នកដោយជោគជ័យ',
                'widget' => $widget
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
            $widget = Widget::with('dashboard')->findOrFail($id);
            return response()->json($widget);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានហ្វីដជេតនេះទេ!'], 404);
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
                    'dashboard_id' => 'sometimes|exists:dashboards,id',
                    'title' => 'sometimes|string|max:255',
                    'description' => 'nullable|string|max:1000',
                    'widget_url' => 'nullable|string|max:500',
                    'axis_x' => 'sometimes|integer|min:0',
                    'axis_y' => 'sometimes|integer|min:0',
                    'width' => 'sometimes|integer|min:1',
                    'height' => 'sometimes|integer|min:1',
                    'component' => 'nullable|string',
                ],
                [
                    'dashboard_id.exists' => 'ទម្រង់នេះមិនមានក្នុងប្រព័ន្ធទេ! (dashboard_id)',
                    'axis_x.integer' => 'ទំហំអ័ក្ស x ត្រូវជាលេខ (axis_x)',
                    'axis_y.integer' => 'ទំហំអ័ក្ស y ត្រូវជាលេខ  (axis_y)',
                    'width.integer' => 'ទំហំហ្វីដជេត ត្រូវជាលេខ (width)',
                    'height.integer' => 'កម្ពស់ហ្វីដជេត ត្រូវជាលេខ (height)',
                    'axis_x.min' => 'ទំហំអ័ក្ស x មិនត្រូវតូចជាង ០ (axis_x)',
                    'axis_y.min' => '​ទំហំអ័ក្ស y មិនត្រូវតូចជាង ០ (axis_y)',
                    'width.min' => 'ទំហំហ្វីដជេត មិនត្រូវតូចជាង ១ (width)',
                    'height.min' => 'កម្ពស់ហ្វីដជេត មិនត្រូវតូចជាង ១ (height)',
                    'widget_url.max' => 'តំណភ្ជាប់ហ្វីដជេត ត្រូវធំជាង ១០០០ (widget_url)',
                    'description.max' => 'ពិពណ៌នា ត្រូវធំជាង ១០០០ (description)',
                    'description.string' => 'ពិពណ៌នា ជាអក្សរ(description)',
                ]
            );

            $widget = Widget::findOrFail($id);
            $widget->update($validatedData);
            $widget->refresh();

            return response()->json([
                'message' => 'កែប្រែជោគជ័យ',
                'widget' => $widget
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានហ្វីដជេតនេះទេ!'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateMultiple(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'widgets' => 'required|array',
                'widgets.*.id' => 'required|exists:widgets,id',
                'widgets.*.dashboard_id' => 'sometimes|exists:dashboards,id',
                'widgets.*.title' => 'sometimes|string|max:255',
                'widgets.*.description' => 'nullable|string|max:1000',
                'widgets.*.question' => 'nullable|integer|min:0',
                'widgets.*.params' => 'nullable|string',
                'widgets.*.axis_x' => 'sometimes|integer|min:0',
                'widgets.*.axis_y' => 'sometimes|integer|min:0',
                'widgets.*.width' => 'sometimes|integer|min:1',
                'widgets.*.height' => 'sometimes|integer|min:1',
                'widgets.*.component' => 'nullable|string',
            ]);

            $updatedWidgets = [];

            foreach ($validatedData['widgets'] as $widgetData) {
                $widget = Widget::findOrFail($widgetData['id']);
                $widget->update($widgetData);
                $updatedWidgets[] = $widget->refresh();
            }

            return response()->json([
                'message' => 'កែប្រែជោគជ័យ',
                'widgets' => $updatedWidgets
            ]);
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
            $widget = Widget::findOrFail($id);
            $widget->delete();

            return response()->json([
                'message' => 'លុបជោគជ័យ',
                $widget
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានហ្វីដជេតនេះទេ!'], 404);
        }
    }
}
