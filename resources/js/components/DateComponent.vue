<template>
    <div>
        <datetime
            type="date"
            v-model="value"
            input-class="form-control"
            valueZone="Asia/Almaty"
            value-zone="Asia/Almaty"
            zone="Asia/Almaty"
            :auto="true"
        ></datetime>
        <input type="hidden" :name="nameProp" :value="value">
    </div>
</template>

<script>
    export default {
        props: ['nameProp', 'valueProp'],
        data() {
            return {
                name: this.nameProp,
                value: this.valueProp,
            }
        },
        watch: { 
            valueProp: function(newVal, oldVal) { // watch it
                console.log('Date New Value: ', newVal, ' | was: ', oldVal);
            }
        },
        mounted() {
        },
        methods: {
        }
    }
</script>

<style scoped>
</style>