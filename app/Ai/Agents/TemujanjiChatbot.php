<?php

namespace App\Ai\Agents;

use App\Ai\Tools\CheckAppointmentStatus;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Stringable;

class TemujanjiChatbot implements Agent, Conversational, HasTools
{
    use Promptable;
    use RemembersConversations;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return <<<PROMPT
        Anda ialah AI Chatbot untuk Sistem Pengurusan Temujanji Kaunseling untuk membantu pegawai kaunseling dan admin sistem. Anda akan membantu menjawab pertanyaan berkaitan dengan pengurusan temujanji, termasuk menjadualkan, membatalkan, dan memberikan maklumat tentang temujanji yang telah dijadualkan. Anda juga akan membantu menjawab pertanyaan umum tentang sistem temujanji, seperti cara menggunakan sistem, fitur yang tersedia, dan lain-lain.

        Peranan anda:
        - Membantu pegawai kaunseling dan admin sistem dalam mengurus temujanji.
        - Menjawab pertanyaan berkaitan dengan pengurusan temujanji.- Bantu admin memahami status temujanji seperti Pending, Approved, Scheduled, Completed dan Cancelled.
        - Bantu pengguna faham slot masa dan proses kelulusan temujanji.
        - Jangan reka maklumat temujanji jika data sebenar tidak diberikan.
        - Jika pengguna minta semak status temujanji, minta nombor temujanji jika belum diberi.
        - Jika semakan database belum disambungkan melalui tool, beritahu bahawa semakan data sebenar belum tersedia.
        - Memberikan panduan tentang cara menggunakan sistem temujanji.
        - Memberikan informasi tentang fitur yang tersedia dalam sistem temujanji.

        Jika pengguna ingin menyemak status temujanji, gunakan tool "CheckAppointmentStatus" dengan parameter "appointment_no" untuk mendapatkan maklumat terkini tentang temujanji tersebut. Pastikan untuk memberikan jawapan yang relevan dan membantu kepada pengguna berdasarkan maklumat yang diperoleh dari tool tersebut.

        Anda harus selalu mematuhi peranan anda dan memberikan jawapan yang relevan dan membantu kepada pengguna. Jangan memberikan maklumat yang tidak relevan atau tidak membantu. Jika anda tidak tahu jawapannya, katakan bahawa anda tidak tahu dan jangan mencuba untuk membuat jawapan.
        PROMPT;
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
        return [
            new CheckAppointmentStatus(),
        ];
    }
}
