<template>
    <div class="schedule-vue-sample">
        <div class="col-lg-9 control-section">
            <div class="content-wrapper">
                <ejs-schedule id='Schedule' height="550px" :selectedDate='selectedDate' :currentView='currentView'
                    :workDays='workDays' :eventSettings='eventSettings' :popupOpen="onPopupOpen"
                    :actionComplete="onActionComplete">
                    <e-views>
                        <e-view option="TimelineDay"></e-view>
                        <e-view option="TimelineWeek"></e-view>
                        <e-view option="TimelineWorkWeek"></e-view>
                        <e-view option="TimelineMonth"></e-view>
                        <e-view option="Agenda"></e-view>
                    </e-views>
                </ejs-schedule>
            </div>
        </div>
        <div class="col-lg-3 property-section">
            <table id="property" title="Properties" style="width: 100%">
                <tbody>
                    <tr style="height: 50px">
                        <td style="width: 100%;">
                            <div>
                                <ejs-datepicker id='datepicker' :value='selectedDate' :showClearButton='false'
                                    :change='onDateChange' floatLabelType="Always"
                                    placeholder="Current Date"></ejs-datepicker>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<style>
.schedule-vue-sample .e-schedule:not(.e-device) .e-agenda-view .e-content-wrap table td:first-child {
    width: 90px;
}

.schedule-vue-sample .e-schedule .e-agenda-view .e-resource-column {
    width: 100px;
}
</style>

<script>
import { extend } from '@syncfusion/ej2-base';
import { scheduleData, timelineData, generateformdataObject } from './datasource';
import { ScheduleComponent, ViewDirective, ViewsDirective, TimelineMonth, TimelineViews, Agenda, Resize, DragAndDrop } from "@syncfusion/ej2-vue-schedule";
import { DatePickerComponent } from '@syncfusion/ej2-vue-calendars';
import MaterialsMixin from "@components/calendar/mixin.js";
export default {
    components: {
        'ejs-schedule': ScheduleComponent,
        'e-view': ViewDirective,
        'e-views': ViewsDirective,
        'ejs-datepicker': DatePickerComponent
    },
    mixins: [MaterialsMixin],
    data: function () {
        return {
            eventSettings: {
                dataSource: '',
            },
            selectedDate: new Date(),
            currentView: 'TimelineWeek',
            workDays: [0, 1, 2, 3, 4, 5]
        }
    },
    provide: {
        schedule: [TimelineViews, TimelineMonth, Agenda, Resize, DragAndDrop]
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
                console.log('timezoneContainer=>', timezoneContainer)
                if (timezoneContainer) {
                    timezoneContainer.style.display = 'none'; // Hides the timezone fields
                }
                console.log("Popup opened for editing/creating an event", args);
            }
        },
        async fetchCalendarData() {
            try {
                const data = await this.getCalenderDatas();  // Wait for the data to be fetched
                //extend([], scheduleData.concat(timelineData), null, true)
                console.log('generateformdataObject(data)=>', generateformdataObject(data))
                this.$set(this, 'eventSettings', { dataSource: generateformdataObject(data) });
            } catch (error) {
                console.error('Error fetching calendar data:', error);
            }
        },
        onActionComplete(args) {
            console.log('args.requestType=>', args.requestType)
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
        onDateChange: function (args) {
            this.selectedDate = args.value;
        }
    },
    created() {
        this.fetchCalendarData();
    },
}
</script>