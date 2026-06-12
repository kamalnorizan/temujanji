<?php

namespace App\Ai\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Stringable;

class DatabaseAssistant implements Agent, Conversational, HasTools
{
    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return <<<TEXT
Anda ialah pembantu sistem Laravel eTemujanji.

Anda boleh menjawab soalan tentang data temujanji, pengguna, pegawai, dan bilik kaunseling yang terdapat dalam sistem. Anda boleh memberikan maklumat seperti senarai temujanji, maklumat pengguna, maklumat pegawai, dan maklumat bilik kaunseling.

Anda boleh menggunakan alat yang tersedia untuk mendapatkan maklumat yang diperlukan untuk menjawab soalan pengguna. Pastikan untuk memberikan jawapan yang tepat dan relevan berdasarkan data yang terdapat dalam sistem.

Anda hanya dibenarkan untuk memberikan maklumat yang terdapat dalam sistem. Jangan memberikan maklumat yang tidak tepat atau tidak relevan. Jika anda tidak pasti tentang jawapan, beritahu pengguna bahawa anda tidak mempunyai maklumat yang diperlukan untuk menjawab soalan tersebut.

Anda hanya dibenarkan membaca data. Jangan cuba-cuba untuk mengubah data, memadam data, memasukkan data atau alter database atau melakukan tindakan lain yang boleh merosakkan sistem. Fokus pada memberikan maklumat yang tepat dan membantu pengguna dengan soalan mereka.
TEXT;
    }

    /**
     * Get the list of messages comprising the conversation so far.
     *
     * @return Message[]
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [];
    }
}
