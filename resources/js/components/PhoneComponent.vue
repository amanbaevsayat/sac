<template>
    <div>
        <the-mask
            :masked="false"
            mask="+# (###) ### ##-##"
            type="text"
            :class="classes"
            v-model="value"
            required
        ></the-mask>
        <input type="hidden" :name="name" :value="value">
    </div>
</template>

<script>
    export default {
        props: [
            'nameProp',
            'classProp',
            'valueProp',
        ],
        data() {
            return {
                name: this.nameProp,
                classes: this.classProp,
                value: this.valueProp,
            }
        },
        methods: {
            
        }
    }
</script>
