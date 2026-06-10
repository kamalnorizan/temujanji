@extends('layouts.app')

@section('content')
    <h1>Pengurusan Temujanji</h1>
    <div class="row">
        <div class="col-md-4">
            <div id="externalevents">
                @foreach ($pendingAppointments as $appointment)
                    <div class="card mb-3 externalevent" data-id="{{ $appointment->id }}" style="cursor: move"
                        data-title="{{ $appointment->purpose }}" data-pemohon="{{ $appointment->name }}" tooltip="{{ $appointment->purpose }}" data-email="{{ $appointment->email }}" data-phoneNumber="{{ $appointment->phone }}">
                        <div class="card-body">
                            <h5 class="card-title "><strong>{{ $appointment->appointment_no }}</strong> -
                                <br>{{ $appointment->purpose }}</h5>
                            <i class="fa fa-eye float-end text-info" aria-hidden="true" style="cursor: pointer"></i>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-md-8">
            <div id="calendar"></div>
        </div>
    </div>

    <!-- Modal Body -->
    <!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
    <div class="modal fade" id="appointment-show" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Perincian Temujanji
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Body</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const showAppt = new bootstrap.Modal(
                document.getElementById("appointment-show")
            );

            $('.externalevent').click(function(e) {
                e.preventDefault();
                const title = $(this).data('title');
                const id = $(this).data('id');
                $('#appointment-show .modal-body').html(`<div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><strong>${title}</strong></h5>
                        <hr>
                        <p class="card-text">Pemohon: ${$(this).data('pemohon')}</p>
                        <p class="card-text">Email: ${$(this).data('email')}</p>
                        <p class="card-text">No. Telefon: ${$(this).data('phoneNumber')}</p>
                    </div>
                </div>`);
                showAppt.show();
            });

            var externalEventsEl = document.getElementById('externalevents');
            var calendarEl = document.getElementById('calendar');

            if (externalEventsEl) {
                new FullCalendar.Draggable(externalEventsEl, {
                    itemSelector: '.externalevent',
                    eventData: function(eventEl) {
                        return {
                            id: eventEl.dataset.id,
                            title: eventEl.dataset.title || eventEl.innerText.trim()
                        };
                    }
                });
            }

            var calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: [
                    FullCalendar.dayGridPlugin,
                    FullCalendar.timeGridPlugin,
                    FullCalendar.interactionPlugin
                ],

                initialView: 'dayGridMonth',

                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },

                editable: true,
                droppable: true,

                eventReceive: function(info) {
                    info.draggedEl.parentNode.removeChild(info.draggedEl);
                }
            });

            calendar.render();
        });
    </script>
@endpush
