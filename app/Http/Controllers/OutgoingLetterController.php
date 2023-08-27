<?php

namespace App\Http\Controllers;

use App\Models\OutgoingLetter;
use App\Enums\LetterType;
use App\Http\Requests\StoreLetterRequest;
use App\Http\Requests\UpdateLetterRequest;
use App\Models\Attachment;
use App\Models\Classification;
use App\Models\Config;
use App\Models\Letter;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class OutgoingLetterController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    return view('pages.transaction.outgoing.index', [
      'data' => Letter::outgoing()->render($request->search),
      'search' => $request->search,
    ]);
  }

  public function agenda(Request $request)
  {
    return view('pages.transaction.outgoing.agenda', [
      'data' => Letter::outgoing()
        ->agenda($request->since, $request->until, $request->filter)
        ->render($request->search),
      'search' => $request->search,
      'since' => $request->since,
      'until' => $request->until,
      'filter' => $request->filter,
      'query' => $request->getQueryString(),
    ]);
  }

  public function print(Request $request)
  {
    $agenda = __('menu.agenda.menu');
    $letter = __('menu.agenda.outgoing_letter');
    $title = App::getLocale() == 'id' ? "$agenda $letter" : "$letter $agenda";
    return view('pages.transaction.outgoing.print', [
      'data' => Letter::outgoing()
        ->agenda($request->since, $request->until, $request->filter)
        ->get(),
      'search' => $request->search,
      'since' => $request->since,
      'until' => $request->until,
      'filter' => $request->filter,
      'config' => Config::pluck('value', 'code')->toArray(),
      'title' => $title,
    ]);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    return view('pages.transaction.outgoing.create', [
      'classifications' => Classification::all(),
    ]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(StoreLetterRequest $request)
  {
    try {
      $user = auth()->user();

      if ($request->type != LetterType::OUTGOING->type()) {
        throw new \Exception(__('menu.transaction.outgoing_letter'));
      }
      $newLetter = $request->validated();
      $newLetter['user_id'] = $user->id;
      $letter = Letter::create($newLetter);
      if ($request->hasFile('attachments')) {
        foreach ($request->attachments as $attachment) {
          $extension = $attachment->getClientOriginalExtension();
          if (!in_array($extension, ['png', 'jpg', 'jpeg', 'pdf'])) {
            continue;
          }
          $filename = time() . '-' . $attachment->getClientOriginalName();
          $filename = str_replace(' ', '-', $filename);
          $attachment->storeAs('public/attachments', $filename);
          Attachment::create([
            'filename' => $filename,
            'extension' => $extension,
            'user_id' => $user->id,
            'letter_id' => $letter->id,
          ]);
        }
      }
      return redirect()
        ->route('transaction.outgoing.index')
        ->with('success', __('menu.general.success'));
    } catch (\Throwable $exception) {
      return back()->with('error', $exception->getMessage());
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(Letter $outgoing)
  {
    return view('pages.transaction.outgoing.show', [
      'data' => $outgoing->load(['classification', 'user', 'attachments']),
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Letter $outgoing)
  {
    return view('pages.transaction.outgoing.edit', [
      'data' => $outgoing,
      'classifications' => Classification::all(),
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(UpdateRequest $request, Letter $outgoing)
  {
    try {
      $outgoing->update($request->validated());
      if ($request->hasFile('attachments')) {
        foreach ($request->attachments as $attachment) {
          $extension = $attachment->getClientOriginalExtension();
          if (!in_array($extension, ['png', 'jpg', 'jpeg', 'pdf'])) {
            continue;
          }
          $filename = time() . '-' . $attachment->getClientOriginalName();
          $filename = str_replace(' ', '-', $filename);
          $attachment->storeAs('public/attachments', $filename);
          Attachment::create([
            'filename' => $filename,
            'extension' => $extension,
            'user_id' => auth()->user()->id,
            'letter_id' => $outgoing->id,
          ]);
        }
      }
      return back()->with('success', __('menu.general.success'));
    } catch (\Throwable $exception) {
      return back()->with('error', $exception->getMessage());
    }
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Letter $outgoing)
  {
    try {
      $outgoing->delete();
      return redirect()
        ->route('transaction.outgoing.index')
        ->with('success', __('menu.general.success'));
    } catch (\Throwable $exception) {
      return back()->with('error', $exception->getMessage());
    }
  }
}
