<template>
    <div>
        <div class="col-sm-12 row nopadding">
            <div class="col-sm-4 nopadding">
                <label class="col-sm-12 col-form-label nopadding" style="margin-bottom: 5px!important">Оператор</label>
                
            </div>
            <div class="col-sm-4 nopadding">
                <label class="col-sm-12 col-form-label nopadding" style="margin-bottom: 5px!important">Бонусная ставка</label>
                
            </div>
            <div class="col-sm-4 nopadding input-group">
                <label class="col-sm-12 col-form-label nopadding" style="margin-bottom: 5px!important">Дата вступления на работу</label>
            </div>
        </div>
        <div class="col-sm-12 row nopadding" v-for="(productUserAccount, productUserId) in productUsers" :key="productUserId">
            <div class="col-sm-4 nopadding">
                <select class="form-control" :name="'productUsers['+ productUserId +'][id]'" id="" v-model="productUserAccount.id">
                    <option v-for="(account, userIndex) in users" :key="userIndex" :value="userIndex">{{ account }}</option>
                </select>
            </div>
            <div class="col-sm-4 nopadding">
                <div style="display: flex">
                    <input type="text" :name="'productUsers['+ productUserId +'][stake]'" id="" v-model="productUserAccount.stake" class="form-control" placeholder="Процентная ставка">
                    <div class="input-group-append">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 nopadding input-group">
                <datetime
                    type="date"
                    v-model="productUserAccount.employment_at"
                    input-class="form-control"
                    valueZone="Asia/Almaty"
                    value-zone="Asia/Almaty"
                    zone="Asia/Almaty"
                    format="dd LLLL yy"
                    :auto="true"
                ></datetime>
                <input type="hidden" :name="'productUsers['+ productUserId +'][employment_at]'" v-model="productUserAccount.employment_at">
                <div class="input-group-text" @click="removeItem(productUserId)">
                    <svg viewBox="0 0 16 16" width="1em" height="1em" focusable="false" role="img" aria-label="x" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi-x b-icon bi"><g><path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path></g></svg>
                </div>
            </div>
        </div>
        <button style="margin-top: 5px" class="btn btn-primary" type="button" @click="addItem">Добавить оператора</button>
    </div>
</template>

<script>
    export default {
        props: ['productUsersProp', 'usersProp'],
        data() {
            return {
                productUsers: this.productUsersProp,
                users: this.usersProp,
            }
        },
        methods: {
            addItem(index) {
                this.productUsers.push({
                    id: null,
                    stake: 0,
                    employment_at: null,
                });
            },
            removeItem(index) {
                if (index > -1) {
                    if (this.productUsers.length > 1) {
                        this.productUsers.splice(index, 1);
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