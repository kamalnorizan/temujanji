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

@push('styles')
<style>
    #chat-container {
        background: linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);
        border-radius: 12px;
    }

    .chat-bubble {
        display: inline-block;
        max-width: 88%;
        padding: 10px 12px;
        border-radius: 12px;
        line-height: 1.45;
        word-break: break-word;
    }

    .chat-bubble-user {
        background: #16a34a;
        color: #fff;
    }

    .chat-bubble-bot {
        background: #fff;
        border: 1px solid #d1d5db;
        color: #111827;
    }

    .chat-bubble-bot p,
    .chat-bubble-bot ul,
    .chat-bubble-bot ol {
        margin-bottom: 0.5rem;
    }

    .chat-bubble-bot p:last-child,
    .chat-bubble-bot ul:last-child,
    .chat-bubble-bot ol:last-child {
        margin-bottom: 0;
    }
</style>
@endpush

@push('scripts')
<script>
    const chatContainer = document.getElementById('chat-container');

    document.getElementById('chat-submit').addEventListener('click', function() {
        appendMessage('user', document.getElementById('chat-input').value);
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Enter' && document.activeElement === document.getElementById('chat-input')) {
            event.preventDefault();
            appendMessage('user', document.getElementById('chat-input').value);
        }
    });

    function escapeHtml(value) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        };

        return value.replace(/[&<>"']/g, function(ch) {
            return map[ch];
        });
    }

    function formatInline(value) {
        let safeText = escapeHtml(value);

        safeText = safeText.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');

        return safeText;
    }

    function renderBotMessage(rawText) {
        const text = String(rawText || '').replace(/\r\n/g, '\n').trim();

        if (text === '') {
            return '<p class="mb-0">Tiada respons diterima.</p>';
        }

        const lines = text.split('\n');
        let html = '';
        let inUl = false;
        let inOl = false;

        const closeLists = () => {
            if (inUl) {
                html += '</ul>';
                inUl = false;
            }

            if (inOl) {
                html += '</ol>';
                inOl = false;
            }
        };

        lines.forEach((line) => {
            const trimmed = line.trim();

            if (trimmed === '') {
                closeLists();
                return;
            }

            const ordered = trimmed.match(/^\d+\.\s+(.*)$/);
            const unordered = trimmed.match(/^[-*]\s+(.*)$/);

            if (ordered) {
                if (!inOl) {
                    closeLists();
                    html += '<ol class="mb-2 ps-3">';
                    inOl = true;
                }

                html += `<li>${formatInline(ordered[1])}</li>`;
                return;
            }

            if (unordered) {
                if (!inUl) {
                    closeLists();
                    html += '<ul class="mb-2 ps-3">';
                    inUl = true;
                }

                html += `<li>${formatInline(unordered[1])}</li>`;
                return;
            }

            closeLists();
            html += `<p>${formatInline(trimmed)}</p>`;
        });

        closeLists();

        return html;
    }

    function appendMessage(sender = 'user', msg = null) {
        const input = document.getElementById('chat-input');
        const message = (msg ?? input.value).trim();
        if (message === '') return;

        // Append user message to chat container
        const userMessage = document.createElement('div');
        userMessage.style.alignSelf = 'flex-end';
        userMessage.classList.add('mb-2', 'text-right', 'float-end', 'clear-both');
        userMessage.innerHTML = `<div class="chat-bubble chat-bubble-user">${escapeHtml(message)}</div>`;
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
                const responsePayload = response.response;
                const responseText = responsePayload?.text ?? responsePayload ?? 'Tiada respons diterima.';

                const botMessage = document.createElement('div');
                botMessage.style.alignSelf = 'flex-start';
                botMessage.classList.add('mb-2', 'text-left', 'float-start', 'clear-both');
                botMessage.innerHTML = `<div class="chat-bubble chat-bubble-bot">${renderBotMessage(responseText)}</div>`;
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
