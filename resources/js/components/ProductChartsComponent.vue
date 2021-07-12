<template>
    <div>
        <div class="input-group col-sm-3 nopadding" v-for="(productChart, index) in productCharts" :key="index">
            <select class="form-control" :name="'productCharts['+ index +'][id]'" id="" v-model="productChart.id">
                <option v-for="(chart, chartIndex) in charts" :key="chartIndex" :value="chartIndex">{{ chart }}</option>
            </select>
            <div class="input-group-text" @click="removeItem(index)">
                <svg viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="x" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-x b-icon bi"><g><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path></g></svg>
            </div>
        </div>
        <button style="margin-top: 5px" class="btn btn-primary" type="button" @click="addItem">Добавить диаграмму</button>
    </div>
</template>

<script>
    export default {
        props: [
            'productChartsProp',
            'chartsProp',
        ],
        data() {
            return {
                productCharts: this.productChartsProp,
                charts: this.chartsProp,
            }
        },
        methods: {
            addItem(index) {
                this.productCharts.push({
                    id: null
                });
            },
            removeItem(index) {
                if (index > -1) {
                    if (this.productCharts.length > 1) {
                        this.productCharts.splice(index, 1);
                    }
                }
            }
        }
    }
</script>
<style scoped>
.nopadding {
   padding: 0 !important;
   margin: 0 !important;
}
</style>