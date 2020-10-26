<template>
    <div>
        <phone-mask-input
            v-model="phone"
            autoDetectCountry
            inputClass="form-control"
        />
        <input :name="name" :value="phone" type="hidden" />
    </div>
</template>

<script>
    import PhoneMaskInput from  "vue-phone-mask-input";
    export default {
        components: {PhoneMaskInput},
        props: [
            'phoneProp',
            'nameProp',
        ],
        data() {
            return {
                phone: this.phoneProp,
                name: this.nameProp,
            };
        }
    }
</script>
