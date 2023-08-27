<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserCntroller extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    return view('pages.user', [
      'data' => User::render($request->search),
      'search' => $request->search,
    ]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(StoreUserRequest $request)
  {
    try {
      $newUser = $request->validated();
      $newUser['password'] = Hash::make(Config::getValueByCode(ConfigEnum::DEFAULT_PASSWORD));
      User::create($newUser);
      return back()->with('success', __('menu.general.success'));
    } catch (\Throwable $exception) {
      return back()->with('error', $exception->getMessage());
    }
  }

  public function update(UpdateUserRequest $request, User $user)
  {
    try {
      $newUser = $request->validated();
      $newUser['is_active'] = isset($newUser['is_active']);
      if ($request->reset_password) {
        $newUser['password'] = Hash::make(Config::getValueByCode(ConfigEnum::DEFAULT_PASSWORD));
      }
      $user->update($newUser);
      return back()->with('success', __('menu.general.success'));
    } catch (\Throwable $exception) {
      return back()->with('error', $exception->getMessage());
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(User $user)
  {
    try {
      $user->delete();
      return back()->with('success', __('menu.general.success'));
    } catch (\Throwable $exception) {
      return back()->with('error', $exception->getMessage());
    }
  }
}
