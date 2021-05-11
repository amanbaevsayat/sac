<template>
    <div>
        <div class="col-sm-4" v-for="(item, index) in productPaymentTypes" :key="index" style="border: 1px solid rgb(179 184 189); padding-top: 15px;margin-bottom: 10px;">
            <div class="input-group col-sm-12" style="margin-bottom: 10px">
                <select class="form-control" :name="'paymentTypes[' + index + '][type]'" v-model="item.type" id="">
                    <option v-for="(option, optionIndex) in paymentTypes" :key="optionIndex" :value="optionIndex">{{ option }}</option>
                </select>
                <div class="input-group-text" @click="removeItem(index)">
                    <svg viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="x" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-x b-icon bi"><g><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path></g></svg>
                </div>
            </div>
            <div class="input-group col-sm-12" v-if="item.type != 'tries'">
                <div class="form-group">
                    <label for="prices" class="col-sm-12 col-form-label nopadding">Бонус за первый платеж</label>
                    <div class="col-sm-12 nopadding">
                        <input class="form-control" :name="'paymentTypes[' + index + '][bonuses][firstPayment]'" v-model="item.bonuses.firstPayment">
                    </div>
                </div>
                <div class="form-group" style="margin-left: 10px;">
                    <label for="prices" class="col-sm-12 col-form-label nopadding">Бонус за повторный платеж</label>
                    <div class="col-sm-12 nopadding">
                        <input class="form-control" :name="'paymentTypes[' + index + '][bonuses][repeatedPayment]'" v-model="item.bonuses.repeatedPayment">
                    </div>
                </div>
            </div>

        </div>
        <button style="margin-top: 5px" class="btn btn-primary" type="button" @click="addItem">Добавить тип оплаты</button>
    </div>
</template>

<script>
    export default {
        props: [
            'productPaymentTypesProp',
            'paymentTypesProp',
        ],
        data() {
            return {
                productPaymentTypes: this.productPaymentTypesProp,
                paymentTypes: this.paymentTypesProp,
            }
        },
        methods: {
            addItem(index) {
                this.productPaymentTypes.push({
                    paymentType: null,
                    bonuses: {
                        firstPayment: null,
                        repeatedPayment: null,
                    }
                });
            },
            removeItem(index) {
                if (index > -1) {
                    if (this.productPaymentTypes.length > 1) {
                        this.productPaymentTypes.splice(index, 1);
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