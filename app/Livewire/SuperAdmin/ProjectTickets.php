<?php

namespace App\Livewire\SuperAdmin;

use App\Models\ProjectTicket;
use App\Models\ProjectTicketReply;
use Livewire\Component;

class ProjectTickets extends Component
{
    public $project;

    public $tickets = [];

    public $newTicketTitle = '';

    public $newTicketDescription = '';

    public $replyContents = [];

    public function mount($project)
    {
        $this->project = $project;
        $this->loadTickets();
    }

    public function loadTickets()
    {
        $this->tickets = ProjectTicket::where('project_id', $this->project->id)
            ->with(['creator', 'replies.user'])
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($this->tickets as $ticket) {
            if (! isset($this->replyContents[$ticket->id])) {
                $this->replyContents[$ticket->id] = '';
            }
        }
    }

    public function createTicket()
    {
        $this->validate([
            'newTicketTitle' => 'required|string|max:255',
            'newTicketDescription' => 'nullable|string',
        ]);

        ProjectTicket::create([
            'project_id' => $this->project->id,
            'creator_id' => auth()->id(),
            'title' => $this->newTicketTitle,
            'description' => $this->newTicketDescription,
            'status' => 'processing',
            'last_reply_at' => now(),
        ]);

        $this->newTicketTitle = '';
        $this->newTicketDescription = '';
        $this->loadTickets();
    }

    public function replyToTicket($ticketId)
    {
        $this->validate([
            "replyContents.$ticketId" => 'required|string',
        ]);

        ProjectTicketReply::create([
            'project_ticket_id' => $ticketId,
            'user_id' => auth()->id(),
            'content' => $this->replyContents[$ticketId],
        ]);

        $ticket = ProjectTicket::find($ticketId);

        $user = auth()->user();
        $isDev = $user->role === 'dev' || $user->hasRole('dev');

        $ticket->update([
            'status' => $isDev ? 'replying' : 'processing',
            'last_reply_at' => now(),
        ]);

        $this->replyContents[$ticketId] = '';
        $this->loadTickets();
    }

    public function closeTicket($ticketId)
    {
        $ticket = ProjectTicket::find($ticketId);
        $ticket->update([
            'status' => 'closed',
        ]);
        $this->loadTickets();
    }

    public function render()
    {
        return view('livewire.superadmin.project-tickets', [
            'tickets' => $this->tickets,
        ]);
    }
}
