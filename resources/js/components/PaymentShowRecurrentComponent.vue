<template>
    <div>
        <div class="form-group row">
            <label for="recurrent" class="col-sm-2 col-form-label">Рекуррентный платеж</label>
            <div class="col-sm-10">
                <input type="checkbox" style="margin-top: 0.3rem" id="recurrent" v-model="data.recurrent" name="recurrent">
            </div>
        </div>
        <div v-if="data.recurrent">
            <div class="form-group row">
                <label for="recurrent" class="col-sm-2 col-form-label">Рекуррентный платеж</label>
                <div class="col-sm-10">
                    <datetime
                        type="datetime"
                        name="start_date"
                        v-model="data.start_date"
                        input-class="my-class form-control"
                        valueZone="Asia/Almaty"
                        zone="Asia/Almaty"
                        format="yyyy-MM-dd HH:mm:ss"
                    ></datetime>
                </div>
            </div>
            <div class="form-group row">
                <label for="period" class="col-sm-2 col-form-label">Период списания следующего платежа</label>
                <div class="col-sm-10">
                    <b-form-select name="period" class="form-control" v-model="data.period" :options="periods"></b-form-select>
                </div>
            </div>
            <div class="form-group row">
                <label for="period" class="col-sm-2 col-form-label">Интервал списания следующего платежа</label>
                <div class="col-sm-10">
                    <b-form-select name="interval" class="form-control" v-model="data.interval" :options="intervals"></b-form-select>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['dataProp'],
        data() {
            return {
                data: this.dataProp,
                periods: [
                    { value: 1, text: 'Один раз' },
                    { value: 2, text: 'Два раза' },
                ],
                intervals: [
                    { value: 'day', text: 'в день' },
                    { value: 'week', text: 'в неделю' },
                    { value: 'month', text: 'в месяц' },
                ],
            }
        },
        mounted() {},
        methods: {
        }
    }
</script>

<style scoped>
</style>
