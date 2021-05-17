<template>
    <div>
        <div class="col-sm-4 nopadding" v-for="(productUserAccount, productUserId) in productUsers" :key="productUserId">
            <div class="input-group col-sm-12 nopadding" style="margin-bottom: 10px">
                <select class="form-control" :name="'productUsers[]'" id="" v-model="productUsers[productUserId]">
                    <option v-for="(account, userIndex) in users" :key="userIndex" :value="userIndex">{{ account }}</option>
                </select>
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
                this.productUsers.push('');
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