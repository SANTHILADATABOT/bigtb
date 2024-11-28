<template>
    <div class="schedule-vue-sample">
        <div class="col-md-12 control-section">
            <div class="content-wrapper">
                <ejs-schedule id='Schedule' ref="ScheduleObj" height="650px" :selectedDate='selectedDate'
                    :eventSettings='eventSettings' :eventRendered="oneventRendered" :popupOpen="onPopupOpen"
                    :actionComplete="onActionComplete"></ejs-schedule>
            </div>
        </div>

    </div>
</template>
<script>
import { extend } from '@syncfusion/ej2-base';
import { zooEventsData, generateformdataObject } from './datasource';
import { ScheduleComponent, Day, Week, WorkWeek, Month, Agenda, Resize, DragAndDrop } from "@syncfusion/ej2-vue-schedule";
import MaterialsMixin from "@components/calendar/mixin.js";
export default {
    components: {
        'ejs-schedule': ScheduleComponent
    },
    mixins: [MaterialsMixin],
    data: function () {
        return {
            // extend([], zooEventsData, null, true)
            eventSettings: { dataSource: '' },
            selectedDate: new Date(),
        }
    },
    provide: {
        schedule: [Day, Week, WorkWeek, Month, Agenda, Resize, DragAndDrop]
    },
    methods: {
        onPopupOpen(args) {
            if (args.type === "Editor") {
                const today = new Date();

                // Set default StartTime and EndTime if not already set
                if (!args.data.StartTime) {
                    args.data.StartTime = today;
                }
                if (!args.data.EndTime) {
                    args.data.EndTime = new Date(today.getTime() + 60 * 60 * 1000); // Default to 1 hour later
                }
                const timezoneContainer = document.querySelector('.e-time-zone-container');
                if (timezoneContainer) {
                    timezoneContainer.style.display = 'none'; // Hides the timezone fields
                }
                console.log("Popup opened for editing/creating an event", args);
            }
        },
        async fetchCalendarData() {
            try {
                const data = await this.getCalenderDatas();  // Wait for the data to be fetched
                this.$set(this, 'eventSettings', { dataSource: generateformdataObject(data) });
            } catch (error) {
                console.error('Error fetching calendar data:', error);
            }
        },
        onActionComplete(args) {
            if (args.requestType === "eventChanged") {
                const updatedEventData = args.data; // Event details
                this.updateEvent(updatedEventData);
            } else if (args.requestType === "eventCreated") {
                const newEventData = args.data; // New Event details
                this.saveEvent(newEventData);
            } else if (args.requestType === "eventRemoved") {
                const removedEventData = args.data; // Data of the event being removed
                this.deleteEvent(removedEventData);
            }
        },
        saveEvent(eventData) {
            var self = this;
            this.show_spinner = true;
            console.log('self.base_url=>', self.base_url);
            console.log('eventData=>', eventData);
            self.httpRequest({
                url: self.base_url + 'pm/v2/monthlycalender/store', // Replace with your API endpoint
                method: "POST",
                data: {
                    title: eventData[0].Subject ?? '',
                    start_date: eventData[0].StartTime,
                    end_date: eventData[0].EndTime,
                    Location: eventData[0].Location,
                    description: eventData[0].Description ?? '',
                    RecurrenceRule: eventData[0].RecurrenceRule ?? '',
                    allDay: eventData[0].IsAllDay ?? '',
                    resources: eventData[0].resources ?? '',
                    created_at: new Date(),
                    RecurrenceID: eventData[0].RecurrenceID ?? null,
                },
                success: function (res) {
                    console.log("Event saved successfully:", res);
                    self.show_spinner = false;
                    self.fetchCalendarData();
                    // Optionally update your store or UI
                },
                error: function (err) {
                    console.error("Error saving event:", err);
                    self.show_spinner = false;
                    self.fetchCalendarData();
                },
            });
        },
        updateEvent(updatedEventData) {
            var self = this;
            this.show_spinner = true;

            // Send updated data to the server
            self.httpRequest({
                url: self.base_url + 'pm/v2/monthlycalender/update', // Replace with your update API endpoint
                method: "POST", // Use PUT or PATCH for updating
                data: {
                    id: updatedEventData[0].Id, // Ensure the ID is passed for the update
                    title: updatedEventData[0].Subject ?? '',
                    DBID: updatedEventData[0].DBID ?? '',
                    start_date: updatedEventData[0].StartTime,
                    end_date: updatedEventData[0].EndTime,
                    Location: updatedEventData[0].Location,
                    description: updatedEventData[0].Description ?? '',
                    RecurrenceRule: updatedEventData[0].RecurrenceRule ?? '',
                    allDay: updatedEventData[0].IsAllDay ?? '',
                    resources: updatedEventData[0].resources ?? '',
                    updated_at: new Date(),
                    RecurrenceId: updatedEventData[0].RecurrenceID ?? null,

                },
                success: function (res) {
                    console.log("Event updated successfully:", res);
                    self.show_spinner = false;

                    // Optionally refresh the data source after updating
                    self.fetchCalendarData();
                },
                error: function (err) {
                    console.error("Error updating event:", err);
                    self.show_spinner = false;
                    self.fetchCalendarData();
                },
            });
        },
        deleteEvent(eventData) {
            var self = this;
            this.show_spinner = true;
            self.httpRequest({
                url: self.base_url + 'pm/v2/monthlycalender/delete', // Replace with your API endpoint
                method: "POST", // DELETE method for deletion
                data: {
                    id: eventData[0].Id, // Ensure the ID is passed for the update
                    title: eventData[0].Subject ?? '',
                    DBID: eventData[0].DBID ?? '',
                    start_date: eventData[0].StartTime,
                    end_date: eventData[0].EndTime,
                    Location: eventData[0].Location,
                    description: eventData[0].Description ?? '',
                    RecurrenceRule: eventData[0].RecurrenceRule ?? '',
                    allDay: eventData[0].IsAllDay ?? '',
                    resources: eventData[0].resources ?? '',
                    updated_at: new Date(),
                    RecurrenceId: eventData[0].RecurrenceID ?? null,
                },
                success: function (res) {
                    console.log("Event deleted successfully:", res.data);
                    self.show_spinner = false;

                    // Optionally refresh the data source after deletion
                    self.fetchCalendarData();
                },
                error: function (err) {
                    console.error("Error deleting event:", err);
                    self.show_spinner = false;
                    self.fetchCalendarData();
                },
            });
        },
        oneventRendered: function (args) {
            let scheduleObj = this.$refs.ScheduleObj;
            let categoryColor = args.data.CategoryColor;
            if (!args.element || !categoryColor) {
                return;
            }
            if (scheduleObj.ej2Instances.currentView === 'Agenda') {
                (args.element.firstChild).style.borderLeftColor = categoryColor;
            } else {
                args.element.style.backgroundColor = categoryColor;
            }

        }
    },
    created() {
        this.fetchCalendarData();
    },
}

</script>