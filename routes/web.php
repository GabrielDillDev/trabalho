<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Note;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::post('/createNote', function (Request $request) {
    Note::create([
        'noteName' => $request->note_name,
        'noteDescription' => $request->note_description
    ]);
    echo "Criado com sucesso!";
});

Route::get('/notes', function () {
    $notes = Note::all();
    return response()->json($notes, 200, [], JSON_UNESCAPED_UNICODE);
});

Route::get('/notes/{noteName}', function ($noteName) {
    $note = Note::where('noteName', $noteName)->firstOrFail();
    return response()->json($note, 200, [], JSON_UNESCAPED_UNICODE);
});

Route::get('/editNote/{id_note}', function ($id_note) {
    $note = Note::findOrFail($id_note);
    return view('edit_note', ['note' => $note]);
});

Route::put('/updateNote/{id_note}', function (Request $request, $id_note) {
    $note = Note::findOrFail($id_note);
    $note->noteName = $request->note_name;
    $note->noteDescription = $request->note_description;
    $note->save();
    return response()->json(['message' => 'Atualizado com sucesso!']);
});


Route::get('/deleteNote/{id_note}', function ($id_note) {
    $note = Note::findOrFail($id_note);
    $note->delete();
    echo "Deletado com sucesso!";
});
