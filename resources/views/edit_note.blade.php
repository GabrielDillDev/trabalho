<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crud</title>
</head>
<body>
    <form action="/updateNote/{{ $note->id }}" method="POST">
        @csrf
        @method("PUT")
        <label for="">Titulo:</label>
        <input type="text" placeholder="Digite o titulo aqui..." name="note_name" value="{{$note->noteName}}">
        <label for="">Descrição:</label>
        <input type="text" placeholder="Digite a descrição aqui..." name="note_description" value="{{$note->noteDescription}}">

        <button>Criar Nota</button>
    </form>
</body>
</html>
