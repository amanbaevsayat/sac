<template>
  <b-container fluid="md" class="timeline-container">
    <timeline-item :dateFormat="dateFormat" :key="item.id" :item="item" v-for="item of items" v-on="$listeners"></timeline-item>
  </b-container>
</template>
<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator';
import { Item } from './simple-timeline-item.model';
import SimpleTimelineItem from './SimpleTimelineItem.vue';

@Component({
  components: {
    timeline: SimpleTimeline,
    timelineItem: SimpleTimelineItem
  }
})
export default class SimpleTimeline extends Vue {
  @Prop({ default: () => [] })
  public items!: Item[];

  @Prop({ default: 'DD/MM/YY' })
  public dateFormat!: string;
}

</script>

