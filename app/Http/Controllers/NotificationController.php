<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class NotificationController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    //
    $user_notifications = Auth::user()->notifications;
    return response()->json([
      'status' => 'success',
      'data' => $user_notifications,
    ]);
  }
  public function unread()
  {
    //

    $notifications = Auth::user()->notifications()->where('is_read', 0)->get();
    return response()->json([
      'status' => 'success',
      'data' => $notifications,
    ]);
  }
  public function no_of_unread()
  {
    //
    $notifications = Auth::user()->notifications()->where('is_read', 0)->count();
    return response()->json([
      'status' => 'success',
      
      'data' => $notifications,
    ]);
  }

  /*
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   */
  public function show($id)
  {
    Auth::user()->notifications()->where('id', $id)
      ->update(['is_read' => 1, 'read_at' => now()]);
    //
    $notification = Auth::user()->notifications()->where('id', $id)->first();
    return response()->json([
      'status' => 'success',
      'data' => $notification,

    ])->setStatusCode(200, 'Marked as read');
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Notification $notification)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Notification $notification)
  {
    //
  }
}
