<template>
    <div class="schedule-vue-sample">
      <div class="e-grid-col-9 control-section">
        <div class="content-wrapper">
          <div class="view-buttons">
            <button class="button-6" @click="setView('Month')" :style="{ backgroundColor: currentView === 'Month' ? '#007bff' : '', color: currentView === 'Month' ? 'white' : '' }">Monthly Calendar</button>
            <button class="button-6" @click="setView('TimelineMonth')" :style="{ backgroundColor: currentView === 'TimelineMonth' ? '#007bff' : '', color: currentView === 'TimelineMonth' ? 'white' : '' }">Monthly Timeline</button>
            <button class="button-6" @click="setView('Week')":style="{ backgroundColor: currentView === 'Week' ? '#007bff' : '', color: currentView === 'Week' ? 'white' : '' }">Weekly Calendar</button>
            <button class="button-6" @click="setView('TimelineResource')" :style="{ backgroundColor: currentView === 'TimelineResource' ? '#007bff' : '', color: currentView === 'TimelineResource' ? 'white' : '' }">Monthly Resource Timeline</button>
            <button class="button-6" @click="setView('resourseTogglepanel')" :style="{ backgroundColor: currentView === 'resourseTogglepanel' ? '#007bff' : '', color: currentView === 'resourseTogglepanel' ? 'white' : '' }">Resource Toogle Panel</button>
            <br /><br />
          </div>
          <component :is="currentViewComponent" />
  
        </div>
      </div>

    </div>
  </template>
<script>
import MonthlyComponent from './Monthlycalender.vue';  // Assuming you have this component
import MonthlyTimelineComponent from './MonthlyTimelinecalender.vue';  // Assuming you have this component
import WeeklyCalendarComponent from './weeklycalender.vue';  // Assuming you have this component
import MonthlyResourceTimelineComponent from './Monthlyresourcetimeline.vue';  // Assuming you have this component
import ResourceTogglePanelComponent from './resourseTogglepanel.vue';  // Assuming you have this component

export default {
  components: {
    MonthlyComponent,  // Register your dynamically rendered components
    MonthlyTimelineComponent,
    WeeklyCalendarComponent,
    MonthlyResourceTimelineComponent,
    ResourceTogglePanelComponent
  },
  data: function () {
    return {
      currentView: 'Month',
    }
  },
  computed: {
    // Dynamically map currentView to the correct component
    currentViewComponent() {
      switch (this.currentView) {
        case 'Month':
          return 'MonthlyComponent';
        case 'TimelineMonth':
          return 'MonthlyTimelineComponent';
        case 'Week':
          return 'WeeklyCalendarComponent';
        case 'TimelineResource':
          return 'MonthlyResourceTimelineComponent';
          case 'resourseTogglepanel':
            return 'ResourceTogglePanelComponent';
        default:
          return 'MonthlyComponent';  // Default to MonthlyComponent if no match
      }
    }
  },
  watch: {
  currentView(newView) {
    console.log("Switched to view:", newView);
  }
},
  methods: {
    setView(view) {
      this.currentView = view;  // Dynamically update the view
    },
  },
  
}

</script>