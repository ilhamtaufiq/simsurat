<?php

namespace App\Http\Controllers;

use App\Models\LetterStatus;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreLetterStatusRequest;
use App\Http\Requests\UpdateLetterStatusRequest;

class LetterStatusController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    return view('pages.reference.status', [
      'data' => LetterStatus::render($request->search),
      'search' => $request->search,
    ]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(StoreLetterStatusRequest $request)
  {
    try {
      LetterStatus::create($request->validated());
      return back()->with('success', __('menu.general.success'));
    } catch (\Throwable $exception) {
      return back()->with('error', $exception->getMessage());
    }
  }

  /**
   * Display the specified resource.
   */
  public function update(UpdateLetterStatusRequest $request, LetterStatus $status)
  {
    try {
      $status->update($request->validated());
      return back()->with('success', __('menu.general.success'));
    } catch (\Throwable $exception) {
      return back()->with('error', $exception->getMessage());
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(LetterStatus $status)
  {
    try {
      $status->delete();
      return back()->with('success', __('menu.general.success'));
    } catch (\Throwable $exception) {
      return back()->with('error', $exception->getMessage());
    }
  }
}
