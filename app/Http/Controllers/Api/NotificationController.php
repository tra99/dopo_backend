<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            $perPage = $request->input('limit', 15);
            $notifications = Notification::where('user_id', auth()->user()->id)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json($notifications);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // View 
    public function show(Request $request, string $id)
    {
        try {
            $notification = Notification::findOrFail($id);
            return response()->json($notification);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានការជូនដំណឹងនេះទេ'], 404);
        }
    }

    // Mark notifications as read
    public function markAsRead(Request $request, string $id)
    {
        try {
            $notification = Notification::findOrFail($id);

            $notification->update(['is_read' => true]);
            $notification->refresh();

            return response()->json([
                $notification
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានការជូនដំណឹងនេះទេ'], 404);
        }   
    }

    // Mark notifications as unread
    public function markAsUnread(Request $request, string $id)
    {
        try {
            $notification = Notification::findOrFail($id);

            $notification->update(['is_read' => false]);
            $notification->refresh();

            return response()->json([
                $notification
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានការជូនដំណឹងនេះទេ'], 404);
        }
    }

    // Mark all notifications as read
    public function markAllAsRead(Request $request)
    {
        try {
            $notifications = Notification::where('user_id', auth()->user()->id)->update(['is_read' => true]);
            return response()->json(['message' => 'ការជូនដំណឹងបានជោគជ័យ!'],
             200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
