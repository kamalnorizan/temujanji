@extends('layouts.app')

@section('content')
    <h1>Pengurusan Temujanji</h1>
    <div class="row">
        <div class="col-md-4">
            <div id="externalevents">
                @foreach ($pendingAppointments as $appointment)
                    <div class="card mb-3 externalevent" data-id="{{ $appointment->id }}" style="cursor: move"
                        data-title="{{ $appointment->purpose }}" data-pemohon="{{ $appointment->name }}"
                        tooltip="{{ $appointment->purpose }}" data-email="{{ $appointment->email }}"
                        data-phoneNumber="{{ $appointment->phone }}">
                        <div class="card-body">
                            <h5 class="card-title "><strong>{{ $appointment->appointment_no }}</strong> -
                                <br>{{ $appointment->purpose }}
                            </h5>
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

    <div class="modal fade" id="appointment-assign" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Tetapan Temujanji
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                            <label for="name">Nama</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                class="form-control" required="required">
                            <small class="text-danger">{{ $errors->first('name') }}</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                                    <label for="phone">No Telefon</label>
                                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                                        class="form-control" required="required">
                                    <small class="text-danger">{{ $errors->first('phone') }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                                        class="form-control" required="required">
                                    <small class="text-danger">{{ $errors->first('email') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('purpose') ? 'has-error' : '' }}">
                            <label for="purpose">Tujuan</label>
                            <input type="text" id="purpose" name="purpose" value="{{ old('purpose') }}"
                                class="form-control" required="required">
                            <small class="text-danger">{{ $errors->first('purpose') }}</small>
                        </div>
                        <div class="form-group {{ $errors->has('notes') ? 'has-error' : '' }}">
                            <label for="notes">Catatan</label>
                            <input type="text" id="notes" name="notes" value="{{ old('notes') }}"
                                class="form-control" required="required">
                            <small class="text-danger">{{ $errors->first('notes') }}</small>
                        </div>
                        <div class="form-group {{ $errors->has('counseling_room_id') ? 'has-error' : '' }}">
                            <label for="counseling_room_id">Bilik Kaunseling</label>
                            <select id="counseling_room_id" name="counseling_room_id" class="form-control"
                                required="required">
                                <option value="">Pilih Bilik Kaunseling</option>
                            </select>
                            <small class="text-danger">{{ $errors->first('counseling_room_id') }}</small>
                        </div>
                        <div class="form-group {{ $errors->has('start_time') ? 'has-error' : '' }}">
                            <label for="start_time">Masa Mula</label>
                            <select id="start_time" name="start_time" class="form-control"
                                required="required">
                                <option value="">Pilih Masa Mula</option>
                            </select>
                            <small class="text-danger">{{ $errors->first('start_time') }}</small>
                        </div>


                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" id="save-appointment" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function formatLocalDate(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');

                return `${year}-${month}-${day}`;
            }

            const showAppt = new bootstrap.Modal(
                document.getElementById("appointment-show")
            );

            const assignAppt = new bootstrap.Modal(
                document.getElementById("appointment-assign")
            );

            let selectedAppointmentId = null;
            let selectedDate = null;
            let selectedCalendarEvent = null;
            let selectedDraggedEl = null;
            let appointmentSaved = false;

            function resetAppointmentSelection() {
                selectedAppointmentId = null;
                selectedDate = null;
                selectedCalendarEvent = null;
                selectedDraggedEl = null;
                appointmentSaved = false;

                $('#counseling_room_id').val('');
                $('#start_time').empty().append('<option value="">Pilih Masa Mula</option>');
            }

            function restorePendingCard() {
                if (selectedDraggedEl && !selectedDraggedEl.isConnected) {
                    document.getElementById('externalevents').appendChild(selectedDraggedEl);
                }
            }

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
            var dateSelected = null;
            var calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: [
                    FullCalendar.dayGridPlugin,
                    FullCalendar.timeGridPlugin,
                    FullCalendar.interactionPlugin
                ],

                initialEvents: @json($calendarEvents),

                initialView: 'dayGridMonth',

                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },

                editable: true,
                droppable: true,

                eventReceive: function(info) {
                    var date = info.event.start;
                    var aptId = info.event.id;
                    appointmentSaved = false;
                    dateSelected = date;
                    selectedDate = date;
                    selectedAppointmentId = aptId;
                    selectedCalendarEvent = info.event;
                    selectedDraggedEl = info.draggedEl;
                    var url = "{{ route('appointments.show', ':id') }}";
                    $.ajax({
                        type: "post",
                        url: url.replace(':id', aptId),
                        data: {
                            _token: "{{ csrf_token() }}",
                            date: formatLocalDate(date)
                        },
                        dataType: "json",
                        success: function(response) {
                    document.getElementById('appointment-assign').addEventListener('hidden.bs.modal', function() {
                        if (!appointmentSaved && selectedCalendarEvent) {
                            selectedCalendarEvent.remove();
                            restorePendingCard();
                        }

                        resetAppointmentSelection();
                    });

                            $('#name').val(response.appointment.name);
                            $('#phone').val(response.appointment.phone);
                            $('#email').val(response.appointment.email);
                            $('#purpose').val(response.appointment.purpose);
                            $('#notes').val(response.appointment.notes);

                            var counselingRooms = response.counseling_rooms;
                            var counselingRoomSelect = $('#counseling_room_id');
                            counselingRoomSelect.empty();
                            counselingRoomSelect.append(
                                '<option value="">Pilih Bilik Kaunseling</option>');
                            $.each(counselingRooms, function(id, name) {
                                counselingRoomSelect.append('<option value="' + id +
                                    '">' + name + '</option>');

                            });
                        }
                    });
                    assignAppt.show();
                }
            });

            calendar.render();

            $('#counseling_room_id').change(function(e) {

                e.preventDefault();
                var counseling_room_id = $(this).val();
                var date = formatLocalDate(dateSelected);
                $.ajax({
                    type: "post",
                    url: "{{ route('appointments.availableTimeCheck') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        date: date,
                        counseling_room_id: counseling_room_id
                    },
                    dataType: "json",
                    success: function(response) {
                        var startTimeSelect = $('#start_time');
                        startTimeSelect.empty();
                        startTimeSelect.append('<option value="">Pilih Masa Mula</option>');
                        $.each(response, function(index, slot) {
                            var option = $('<option></option>')
                                .attr('value', slot.time)
                                .text(slot.time);

                            if (!slot.available) {
                                option.attr('disabled', true);
                                option.text(slot.time + ' (Penuh)');
                            }

                            startTimeSelect.append(option);
                        });
                    }
                });
            });

            $('#save-appointment').click(function(e) {
                e.preventDefault();

                if (!selectedAppointmentId || !selectedDate) {
                    alert('Sila pilih temujanji dan tarik ke kalender dahulu.');
                    return;
                }

                var url = "{{ route('appointments.update', ':id') }}";

                $.ajax({
                    type: "patch",
                    url: url.replace(':id', selectedAppointmentId),
                    data: {
                        _token: "{{ csrf_token() }}",
                        name: $('#name').val(),
                        phone: $('#phone').val(),
                        email: $('#email').val(),
                        purpose: $('#purpose').val(),
                        notes: $('#notes').val(),
                        counseling_room_id: $('#counseling_room_id').val(),
                        start_time: $('#start_time').val(),
                        scheduled_date: formatLocalDate(selectedDate)
                    },
                    dataType: "json",
                    success: function(response) {
                        appointmentSaved = true;
                        assignAppt.hide();

                        if (selectedCalendarEvent) {
                            selectedCalendarEvent.setProp('title', response.appointment.purpose || response.appointment.name);
                        }

                        if (selectedDraggedEl && selectedDraggedEl.parentNode) {
                            selectedDraggedEl.parentNode.removeChild(selectedDraggedEl);
                        }
                        alert(response.message);
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errorMessages = Object.values(xhr.responseJSON.errors).flat().join('\n');
                            alert(errorMessages);
                            return;
                        }

                        alert('Gagal menyimpan temujanji. Sila cuba lagi.');
                    }
                });
            });
        });


    </script>
@endpush
