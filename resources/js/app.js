import './bootstrap';
import './jquery-global';
import 'admin-lte';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin, { Draggable } from '@fullcalendar/interaction';

window.FullCalendar = {
    Calendar: Calendar,
    Draggable: Draggable,
    dayGridPlugin: dayGridPlugin,
    timeGridPlugin: timeGridPlugin,
    interactionPlugin: interactionPlugin,
};
