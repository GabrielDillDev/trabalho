<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Notas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #f0f0f0;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
            width: 400px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h1 {
            margin-bottom: 20px;
        }
        form {
            width: 100%;
        }
        label {
            display: block;
            margin-bottom: 8px;
        }
        input {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .message {
            margin-top: 20px;
        }
        .notes {
            width: 100%;
        }
        .note {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .note-buttons button {
            margin-left: 5px;
        }
        #editForm {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Criar Nota</h1>
        <form id="noteForm">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <label for="note_name">Título:</label>
            <input type="text" placeholder="Digite o título aqui..." name="note_name" id="note_name" required>
            
            <label for="note_description">Descrição:</label>
            <input type="text" placeholder="Digite a descrição aqui..." name="note_description" id="note_description" required>
            
            <button type="submit">Criar Nota</button>
        </form>
        <div class="message" id="message"></div>
    </div>
    <div class="container">
        <h1>Notas</h1>
        <div class="notes" id="notes"></div>
    </div>
    <div class="container" id="editForm">
        <h1>Editar Nota</h1>
        <form id="updateForm">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="note_id" id="edit_note_id">
            <label for="edit_note_name">Título:</label>
            <input type="text" placeholder="Digite o título aqui..." name="note_name" id="edit_note_name" required>
            
            <label for="edit_note_description">Descrição:</label>
            <input type="text" placeholder="Digite a descrição aqui..." name="note_description" id="edit_note_description" required>
            
            <button type="submit">Atualizar Nota</button>
        </form>
        <div class="message" id="edit_message"></div>
    </div>

    <script>
        function fetchNotes() {
            fetch('/notes')
                .then(response => response.json())
                .then(notes => {
                    const notesDiv = document.getElementById('notes');
                    notesDiv.innerHTML = '';
                    notes.forEach(note => {
                        const noteDiv = document.createElement('div');
                        noteDiv.className = 'note';
                        noteDiv.innerHTML = `
                            <div>
                                <strong>${note.noteName}</strong>
                                <p>${note.noteDescription}</p>
                            </div>
                            <div class="note-buttons">
                                <button onclick="editNoteForm(${note.id}, '${note.noteName}', '${note.noteDescription}')">Editar</button>
                                <button onclick="deleteNote(${note.id})">Deletar</button>
                            </div>
                        `;
                        notesDiv.appendChild(noteDiv);
                    });
                });
        }

        document.getElementById('noteForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const noteName = document.getElementById('note_name').value;
            const noteDescription = document.getElementById('note_description').value;
            const token = document.querySelector('input[name="_token"]').value;

            fetch('/createNote', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    note_name: noteName,
                    note_description: noteDescription
                })
            })
            .then(response => response.text())
            .then(data => {
                const messageDiv = document.getElementById('message');
                if (data.includes('Criado com sucesso')) {
                    messageDiv.innerHTML = '<p style="color: green;">' + data + '</p>';
                    fetchNotes();
                } else {
                    messageDiv.innerHTML = '<p style="color: red;">Erro ao adicionar nota.</p>';
                }
                document.getElementById('noteForm').reset();
            })
            .catch(error => {
                const messageDiv = document.getElementById('message');
                messageDiv.innerHTML = '<p style="color: red;">Erro ao adicionar nota.</p>';
                console.error('Erro:', error);
            });
        });

        function deleteNote(id) {
            fetch(`/deleteNote/${id}`)
                .then(response => response.text())
                .then(data => {
                    const messageDiv = document.getElementById('message');
                    if (data.includes('Deletado com sucesso')) {
                        messageDiv.innerHTML = '<p style="color: green;">' + data + '</p>';
                        fetchNotes();
                    } else {
                        messageDiv.innerHTML = '<p style="color: red;">Erro ao deletar nota.</p>';
                    }
                })
                .catch(error => {
                    const messageDiv = document.getElementById('message');
                    messageDiv.innerHTML = '<p style="color: red;">Erro ao deletar nota.</p>';
                    console.error('Erro:', error);
                });
        }

        function editNoteForm(id, name, description) {
            document.getElementById('editForm').style.display = 'block';
            document.getElementById('edit_note_id').value = id;
            document.getElementById('edit_note_name').value = name;
            document.getElementById('edit_note_description').value = description;
            window.scrollTo(0, 0);
        }

        document.getElementById('updateForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const noteId = document.getElementById('edit_note_id').value;
            const noteName = document.getElementById('edit_note_name').value;
            const noteDescription = document.getElementById('edit_note_description').value;
            const token = document.querySelector('input[name="_token"]').value;

            fetch(`/updateNote/${noteId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    note_name: noteName,
                    note_description: noteDescription
                })
            })
            .then(response => response.json())
            .then(data => {
                const messageDiv = document.getElementById('edit_message');
                if (data.message.includes('Atualizado com sucesso')) {
                    messageDiv.innerHTML = '<p style="color: green;">' + data.message + '</p>';
                    fetchNotes();
                } else {
                    messageDiv.innerHTML = '<p style="color: red;">Erro ao atualizar nota.</p>';
                }
                document.getElementById('updateForm').reset();
                document.getElementById('editForm').style.display = 'none';
            })
            .catch(error => {
                const messageDiv = document.getElementById('edit_message');
                messageDiv.innerHTML = '<p style="color: red;">Erro ao atualizar nota.</p>';
                console.error('Erro:', error);
            });
        });

        fetchNotes();
    </script>
</body>
</html>
