@extends('layouts.app')

@section('content')
    <h1>Chatbot Temujanji</h1>
    <div class="card">
        <div class="card-header">
            <div class="card-title">

                Chatbot Temujanji
            </div>
            <div class="card-tools">
                <a href="{{ route('appointments.index') }}" class="btn btn-secondary">Kembali ke Senarai Temujanji</a>
            </div>
        </div>
        <div class="card-body">
            <div id="chat-container" class="mb-3" style="height: 400px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; display: flex; flex-direction: column;">
                <!-- Chat messages will be appended here -->
            </div>
            {{-- <form id="chat-form"> --}}
                <div class="input-group">
                    <input type="text" id="chat-input" class="form-control" placeholder="Taip mesej anda..." required>
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button" id="chat-submit">Hantar</button>
                    </div>
                </div>
            {{-- </form> --}}
        </div>
    </div>

@endsection

@push('scripts')
<script>
    document.getElementById('chat-submit').addEventListener('click', function() {
        appendMessage('user', document.getElementById('chat-input').value);
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Enter' && document.activeElement === document.getElementById('chat-input')) {
            event.preventDefault();
            appendMessage('user', document.getElementById('chat-input').value);
        }
    });

    function appendMessage(sender = 'user', msg = null) {
        const input = document.getElementById('chat-input');
        const message = msg || input.value.trim();
        if (message === '') return;

        // Append user message to chat container
        const chatContainer = document.getElementById('chat-container');
        const userMessage = document.createElement('div');
        userMessage.style.alignSelf = 'flex-end';
        userMessage.classList.add('mb-2', 'text-right', 'float-end', 'clear-both');
        userMessage.innerHTML = `<span class="badge bg-success"> ${message}</span>`;
        chatContainer.appendChild(userMessage);
        chatContainer.scrollTop = chatContainer.scrollHeight;

        // Clear input field
        input.value = '';
        // Send message to server

        fetch('{{ route('appointments.chatbot.sendMessage') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(response => {
            if (response.success) {
                const botMessage = document.createElement('div');
                botMessage.style.alignSelf = 'flex-start';
                botMessage.classList.add('mb-2', 'text-left', 'float-start', 'clear-both');
                    botMessage.innerHTML = `<span class="badge bg-secondary"> ${response.response}</span>`;
                    chatContainer.appendChild(botMessage);
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                } else {
                    alert('Gagal mendapatkan respons dari AI: ' + response.message);
                }
            })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal mendapatkan respons dari AI. Sila cuba lagi.');
        });
    }
</script>
@endpush
