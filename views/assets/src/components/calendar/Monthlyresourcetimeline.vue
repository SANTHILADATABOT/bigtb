<template>
    <div class="schedule-vue-sample">
        <div class="col-md-12 control-section">
            <div class="schedule-container">
                <ejs-schedule id='Schedule' ref="ScheduleObj" height="650px" :cssClass="cssClass"
                    :selectedDate="selectedDate" :eventSettings="eventSettings" :group="group"
                    :currentView="currentView" :resourceHeaderTemplate="resourceHeaderTemplate" :popupOpen="onPopupOpen">
                    <!-- <template v-slot:resourceHeaderTemplate>
                    <div v-for="data in employeeDataSource" :key="data.Id" class="template-wrap">
                    <div class="employee-category">
                        <div class="employee-name">{{ getEmployeeName(data) }}</div>
                        <div class="employee-designation">{{ getEmployeeDesignation(data) }}</div>
                    </div>
                    </div>
                </template> -->
                    <e-views>
                        <e-view option="Day"></e-view>
                        <e-view option="TimelineDay"></e-view>
                        <e-view option="TimelineMonth"></e-view>
                    </e-views>
                    <e-resources>
                        <e-resource field='EmployeeId' title='Employees' name='Employee'
                            :dataSource='employeeDataSource' textField='Text' idField='Id' groupIDField='GroupId'
                            colorField='Color'>
                        </e-resource>
                    </e-resources>
                </ejs-schedule>
            </div>

        </div>
    </div>
</template>
<style>
.schedule-vue-sample .block-events.e-schedule .template-wrap {
    width: 100%;
}

.schedule-vue-sample .block-events.e-schedule .e-vertical-view .e-resource-cells {
    height: 58px;
}

.schedule-vue-sample .block-events.e-schedule .e-timeline-view .e-resource-left-td,
.schedule-vue-sample .block-events.e-schedule .e-timeline-month-view .e-resource-left-td {
    width: 170px;
}

.schedule-vue-sample .block-events.e-schedule .e-resource-cells.e-child-node .employee-category,
.schedule-vue-sample .block-events.e-schedule .e-resource-cells.e-child-node .employee-name {
    padding: 5px
}

.schedule-vue-sample .block-events.e-schedule .employee-image {
    width: 45px;
    height: 40px;
    float: left;
    border-radius: 50%;
    margin-right: 10px;
}

.schedule-vue-sample .block-events.e-schedule .employee-name {
    font-size: 13px;
}

.schedule-vue-sample .block-events.e-schedule .employee-designation {
    font-size: 10px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: normal;
}

@media (max-width: 550px) {
    .e-schedule .e-day-view .employee-image {
        display: none;
    }

    .e-schedule .e-day-view .employee-name {
        font-size: 14px;
    }

    .e-schedule .e-day-view .employee-designation {
        display: none;
    }
}
</style>

<script>
import { extend } from '@syncfusion/ej2-base';
import { blockData } from './datasource';
import { ScheduleComponent, ViewDirective, ViewsDirective, ResourceDirective, ResourcesDirective, Day, TimelineViews, TimelineMonth, Resize, DragAndDrop } from "@syncfusion/ej2-vue-schedule";

export default {
    components: {
        'ejs-schedule': ScheduleComponent,
        'e-view': ViewDirective,
        'e-views': ViewsDirective,
        'e-resource': ResourceDirective,
        'e-resources': ResourcesDirective
    },
    data: function () {
        return {
            eventSettings: {
                dataSource: extend([], blockData, null, true)
            },
            selectedDate: new Date(2021, 7, 2),
            currentView: 'TimelineDay',
            cssClass: 'block-events',
            group: {
                enableCompactView: false,
                resources: ['Employee']
            },
            employeeDataSource: [
                { Text: 'Alice', Id: 1, GroupId: 1, Color: '#bbdc00', Designation: 'Content writer' },
                { Text: 'Nancy', Id: 2, GroupId: 2, Color: '#9e5fff', Designation: 'Designer' },
                { Text: 'Robert', Id: 3, GroupId: 1, Color: '#bbdc00', Designation: 'Software Engineer' },
                { Text: 'Robson', Id: 4, GroupId: 2, Color: '#9e5fff', Designation: 'Support Engineer' },
                { Text: 'Laura', Id: 5, GroupId: 1, Color: '#bbdc00', Designation: 'Human Resource' },
                { Text: 'Margaret', Id: 6, GroupId: 2, Color: '#9e5fff', Designation: 'Content Analyst' }
            ]
        }
    },
    provide: {
        schedule: [Day, TimelineViews, TimelineMonth, Resize, DragAndDrop]
    },
    computed: {
        getImage() {
            return (data) => {
                return 'source/schedule/images/' + this.getEmployeeName(data).toLowerCase() + '.png';
            };
        }
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
            }
        },
        getEmployeeName: function (data) {
            let value = JSON.parse(JSON.stringify(data));
            return value.Text ?? '';
        },
        getEmployeeDesignation: function (data) {
            let value = JSON.parse(JSON.stringify(data));
            console.log('value.resourceData.Designation=>', value);
            return value.Designation ?? '';
        }
    }
}

</script>