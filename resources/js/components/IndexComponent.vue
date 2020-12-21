<template>
    <div>
        <div class="row">
            <div class="col-12" v-if="mainFilters.length > 0">
                <div class="card mb-2" id="filter">
                    <div 
                        style="line-height: 25px; cursor: pointer;" 
                        @click="filterOpen = !filterOpen" 
                        class="card-header py-1"
                    >
                        Фильтр
                        <small class="float-right">
                            <a id="filter-toggle" class="btn btn-default btn-sm" title="Скрыть/показать">
                                <i class="fa fa-toggle-off " :class="{'fa-toggle-on': filterOpen}"></i>
                            </a>
                        </small>
                    </div>
                    <div class="card-body" v-show="filterOpen" :class="{slide: filterOpen}">
                        <div class="row">
                            <div class="col-3" v-for="(filter, filterIndex) in mainFilters" :key="filterIndex">
                                <div class="form-group">
                                    <label for="subscriptionStatus">{{ filter.title }}</label>
                                    <div v-if="filter.type == 'select'">
                                        <select @change.capture="changeSelect($event, filter.name)" v-model="queryParams[filter.name]" :name="filter.name" :id="filter.name" class="form-control" data-live-search="true" data-dropdown-align-right="true" data-style="select-with-transition" data-size="7">
                                            <option v-for="(option, optionIndex) in filter.options" :key="optionIndex" :value="optionIndex">{{ option }}</option>
                                        </select>
                                    </div>
                                    <div v-if="filter.type == 'select-multiple'">
                                        <!-- <select v-model="queryParams[filter.name]" :name="filter.name" :id="filter.name" class="form-control" multiple>
                                            <option v-for="option in filter.options" :key="option.value" :value="option.value">{{ option.text }}</option>
                                        </select> -->
                                        <b-form-select class="select-multiple" v-model="queryParams[filter.name]" :options="filter.options" multiple :select-size="4"></b-form-select>
                                        <!-- <v-select v-model="queryParams[filter.name]" :options="filter.options" multiple :reduce="field => field.value" :filterable="false">
                                            
                                        </v-select> -->
                                    </div>
                                    <div v-if="filter.type == 'select-search'">
                                        <v-select v-model="queryParams[filter.name]" :reduce="field => field.value" :filterable="false" :options="options[filter.key]" @search="onSearch(...arguments, filter.key)">
                                            <template slot="no-options">
                                            Введите для поиска
                                            </template>
                                            <template slot="option" slot-scope="option">
                                                <div class="d-center">
                                                    {{ option.label }}
                                                    </div>
                                            </template>
                                            <template slot="selected-option" slot-scope="option">
                                                <div class="selected d-center">
                                                    {{ option.label }}
                                                </div>
                                            </template>
                                        </v-select>
                                    </div>
                                    <div v-if="filter.type == 'input'">
                                        <input class="form-control" type="text" :name="filter.name" :value="queryParams[filter.name]" @change="changeFilterValue($event, filter.name, filter.type)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-2">
            <div class="input-group" >
                <div style="flex: 1 1 auto; margin-left: 7px; margin-right: 10px" v-for="(filter, filterIndex) in secondFilters" :key="filterIndex">
                    <input
                        v-if="filter.type == 'input-search'"
                        type="text" 
                        class="form-control" 
                        :name="filter.name" 
                        :value="queryParams[filter.name]"
                        @keydown="inputSearch($event, filter.name, filter.type, 'keydown')"
                        @input="inputSearch($event, filter.name, filter.type, 'input')"
                        :placeholder="filter.placeholder" 
                        aria-label="search" 
                        aria-describedby="search-icon"
                    >
                </div>
                <button v-if="mainFilters.length > 0 || secondFilters.length > 0" type="button" class="btn btn-success btn-sm" @click="applyFilter()">Найти</button>
                <button v-if="mainFilters.length > 0 || secondFilters.length > 0" type="button" style="margin: 0 7px" class="btn btn-dark btn-sm" @click="resetFilter()">Сброс фильтра</button>
                <button-customer-component v-if="prefix == 'customers' || prefix == 'subscriptions'"></button-customer-component>
                <a v-else-if="prefix != 'payments'" :href="createLink" class="btn btn-info text-white" title="Добавить">
                    <i class="fa fa-plus"></i>
                </a>
                <div style="flex: 0 1 auto; margin-left: 7px; line-height: 28px; margin-right: 10px;">
                    <span>
                        Показано с {{ pagination.from }} до {{ pagination.to }} из {{ pagination.total }} совпадений
                    </span>
                </div>
            </div>
        </div>
        <div style="overflow: hidden!important" class="table-responsive bg-white">
            <pulse-loader class="spinner" :loading="spinnerData.loading" :color="spinnerData.color" :size="spinnerData.size"></pulse-loader>
            <table style="overflow: hidden" class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th scope="col">
                            <span>
                                #
                            </span>
                        </th>
                        <th scope="col" v-for="(item, dataTitlesIndex) in dataTitles" :key="dataTitlesIndex">
                            <a v-if="item.key" class="thead-title" @click="changeSortQueryParams(item.key)">
                                {{ item.title }}
                            </a>
                            <span v-else>{{ item.title }}</span>
                        </th>
                        <th scope="col">
                            <!-- <i class="fa fa-cog"></i> -->
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(items, itemsIndex) in data" :key="itemsIndex" :data-id="itemsIndex">
                        <td>
                            <div>
                                <div class="custom-text">
                                    {{ itemsIndex + pagination.from }}
                                </div>
                            </div>
                        </td>
                        <td v-for="(item, name) in items" :key="name" :class="{ editable: item.type, link: item.type == 'link', tdhidden: item.type == 'hidden' }">
                            <div v-if="item.type == 'hidden'">
                            </div>
                            <div v-else-if="item.type == 'select'">
                                <select :name="name" v-model="item.value" class="form-control form-control-sm">
                                    <option v-for="(collection, collectionIndex) in others[item.collection]" :key="collectionIndex" :value="collectionIndex">
                                        {{ collection }}
                                    </option>
                                </select>
                            </div>
                            <div v-else-if="item.type == 'input'">
                                <input type="text" class="form-control form-control-sm" v-model="item.value" />
                            </div>
                            <div v-else-if="item.type == 'customer-link'">
                                <a class="custom-link" role="button" @click="openModal(item.id)" data-toggle="modal" data-target="#modal-customer-edit">{{ item.title }}</a>
                            </div>
                            <div v-else-if="item.type == 'link'">
                                <a class="custom-link" :href="item.value" role="button">{{ item.title }}</a>
                            </div>
                            <div v-else-if="item.type == 'date'">
                                <datetime
                                    type="date"
                                    v-model="item.value"
                                    input-class="my-class form-control"
                                    valueZone="Asia/Almaty"
                                    zone="Asia/Almaty"
                                    format="dd LLLL"
                                ></datetime>
                            </div>
                            <div v-else>
                                <div class="custom-text" v-if="name == 'status'">
                                    <span class="status" :class="{ 
                                        'status-tries': item.value == 'Пробует', 
                                        'status-waiting': item.value == 'Жду оплату', 
                                        'status-paid': item.value == 'Оплачено',
                                        'status-refused': item.value == 'Отказался'
                                    }">
                                        {{ item.value }}
                                    </span>
                                </div>
                                <div class="custom-text" v-else>
                                    {{ item.value }}
                                </div>
                            </div>
                        </td>
                        <td class="text-right" v-if="prefix != 'payments'">
                            <button v-if="false" @click="saveItem(items, items.id.value)" type="button" class="btn btn-danger btn-sm save-button" title="Сохранить">
                                <i class="fa fa-save"></i>
                            </button>
                            <div class="dropdown btn-group" role="group" v-if="prefix != 'subscriptions'">
                                <button class="btn btn-outline-dark btn-sm dropdown-toggle" type="button" :id="'dropdownMenuButton' + itemsIndex" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-cog"></i>
                                </button>
                                <div class="dropdown-menu" :aria-labelledby="'dropdownMenuButton' + itemsIndex">
                                    <a v-if="prefix != 'payments'" :href="'/'+ prefix +'/'+ items.id.value +'/edit'" class="dropdown-item" title="Редактировать">
                                        Редактировать
                                    </a>
                                    <a :href="'/'+ prefix +'/'+ items.id.value" class="dropdown-item" title="Подробнее">
                                        Подробнее
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <nav aria-label="Статьи по Bootstrap 4">
            <ul class="pagination justify-content-center">
                <li class="page-item" :class="{ disabled: pagination.current_page == 1 }">
                    <a class="page-link" @click="changePage(1)">
                        Первая
                    </a>
                </li>
                <li class="page-item" :class="{ disabled: pagination.current_page == 1 }">
                    <a class="page-link" @click="changePage(pagination.current_page - 1)" aria-label="Предыдущая">
                        <span aria-hidden="true">«</span>
                        <span class="sr-only">Предыдущая</span>
                    </a>
                </li>
                <li class="page-item" v-if="pagination.current_page > 2">
                    <a class="page-link" @click="changePage(pagination.current_page - 2)">{{ pagination.current_page - 2 }}</a>
                </li>
                <li class="page-item" v-if="pagination.current_page > 1">
                    <a class="page-link" @click="changePage(pagination.current_page - 1)">{{ pagination.current_page - 1 }}</a>
                </li>
                <li class="page-item active">
                    <a class="page-link disabled">{{ pagination.current_page }}</a>
                </li>
                <li class="page-item" v-if="(pagination.last_page - pagination.current_page) > 0">
                    <a class="page-link" @click="changePage(pagination.current_page + 1)">{{ pagination.current_page + 1 }}</a>
                </li>
                <li class="page-item" v-if="(pagination.last_page - pagination.current_page) > 1">
                    <a class="page-link" @click="changePage(pagination.current_page + 2)">{{ pagination.current_page + 2 }}</a>
                </li>
                <li class="page-item" :class="{ disabled: pagination.last_page == pagination.current_page}">
                    <a class="page-link" @click="changePage(pagination.current_page + 1)" aria-label="Следующая">
                        <span aria-hidden="true">»</span>
                        <span class="sr-only">Следующая</span>
                    </a>
                </li>
                <li class="page-item" :class="{ disabled: pagination.last_page == pagination.current_page}">
                    <a class="page-link" @click="changePage(pagination.last_page)">
                        Последняя
                    </a>
                </li>
            </ul>
        </nav>
        <customer-component type-prop="edit" :customer-id-prop="customerId"></customer-component>
    </div>
</template>

<script>
import CustomerComponent from './CustomerComponent.vue';
import ButtonCustomerComponent from './ButtonCustomerComponent.vue';
    export default {
  components: { CustomerComponent },
        props: [
            'prefixProp',
            'createLinkProp',
        ],
        data() {
            return {
                createLink: this.createLinkProp,
                filterOpen: false,
                options: {
                    customer: [],
                },
                customerId: null,
                prefix: this.prefixProp,
                mainFilters: {},
                secondFilters: {},
                data: {},
                dataTitles: {},
                others: {},
                pagination: {},
                queryParams: {},
                spinnerData: {
                    loading: false,
                    color: '#6cb2eb',
                    size: '100px',
                },
            }
        },
        mounted() {
            this.$nextTick(function(){
                this.spinnerData.loading = true;
                this.getFilters();
                // this.setQueryParams();
                this.getData();
            });
            // let externalScript = document.createElement('script');
            // externalScript.setAttribute('src', 'https://widget.cloudpayments.ru/bundles/cloudpayments');
            // document.head.appendChild(externalScript);
        },
        methods: {
            inputSearch(event, filterName, filterType, type) {
                console.log(type);
                this.queryParams[filterName] = event.target.value;
                this.setAddressBar();
                if (event.target.value.length > 1) {
                    this.getData();
                }
            },
            changeSelect(e, index) {
                console.log(123);
            },
            onSearch(search, loading, key) {
                console.log(search, key);
                loading(true);
                this.search(loading, search, this, key);
            },
            search: _.debounce((loading, search, vm, key) => {
                fetch(
                    '/search?' + $.param({key: key, text: search})
                ).then(res => {
                    res.json().then(json => (vm.options[key] = json.data));
                    loading(false);
                });
            }, 350),
            openModal(customerId) {
                this.customerId = customerId;
            },
            saveItem(items, id) {
                let data = {};
                Object.keys(items).forEach(function(name) {
                    data[name] = items[name].value;
                });
                this.spinnerData.loading = true;
                axios.put(`/${this.prefix}/${id}`, data)
                    .then(response => {
                        this.spinnerData.loading = false;
                        if (response.data.message) {
                            Vue.$toast.success(response.data.message);
                        }
                    })
                    .catch(err => {
                        this.spinnerData.loading = false;
                        if (err.response.status === 422) {
                            let errors = err.response.data.errors;
                            if (errors) {
                                Object.keys(errors).forEach(function(name) {
                                    Vue.$toast.error(errors[name][0]);
                                });
                            }
                        }
                        throw err;
                    });
            },
            changePage(number) {
                this.queryParams.page = number;
                this.setAddressBar();
                this.getData();
            },
            applyFilter() {
                this.setAddressBar();
                this.getData();
            },
            resetFilter() {
                this.queryParams = {},
                this.setAddressBar();
                this.getData();

                let multipleSelects = $(".select-multiple");
                [].forEach.call(multipleSelects, function (select) {
                    
                    let element = select.options;
                    for(var i = 0; i < element.length; i++){
                        element[i].selected = false;
                    }
                });
            },
            changeSortQueryParams(key) {
                if ('sort' in this.queryParams) {
                    let sortType = '';
                    let matches = this.queryParams.sort.match(/\((.*?)\)/);
                    if (matches) {
                        sortType = matches[1];
                    }
                    let sortKey = this.queryParams.sort.replace(/\([^)]+\)/, '');
                    
                    if (sortType == 'asc') {
                        this.queryParams['sort'] = key + '(desc)';
                    } else {
                        this.queryParams['sort'] = key + '(asc)';
                    }
                } else {
                    this.queryParams['sort'] = key + '(asc)';
                }

                this.setAddressBar();
                this.getData();
            },
            changeFilterValue(e, filterName, filterType) {
                this.queryParams[filterName] = e.target.value;
            },
            setAddressBar() {
                if (Object.keys(this.queryParams).length > 0) {
                    let strQueryParams = new URLSearchParams(this.queryParams).toString()
                    history.pushState('', '', '?' + strQueryParams);
                } else {
                    history.pushState('', '', location.origin + location.pathname);
                }
            },
            setQueryParams() {
                const params = new URLSearchParams(window.location.search);

                const paramsObj = Array.from(params.keys()).reduce(
                    (acc, val) => ({ ...acc, [val]: params.get(val) }),
                    {}
                );
                let selectMultipleNames = [];
                this.mainFilters.forEach(function(filter) {
                    if (filter.type == 'select-multiple') {
                        selectMultipleNames.push(filter.name);
                    }
                });
                let data = paramsObj;
                Object.keys(data).map(function(key, index) {
                    let value = null;
                    if (data[key].includes(",")) {
                        value = data[key].split(",");
                    } else {
                        value = data[key];
                    }
                    
                    if (typeof value === 'string' && selectMultipleNames.includes(key)) {
                        value = [value];
                    }
                    data[key] = value;
                });
                this.queryParams = data;
            },
            getData: _.debounce(function () {
                this.spinnerData.loading = true;
                axios.get(`/${this.prefixProp}/list`, { params: this.queryParams }).then(response => {
                    this.data = response.data.data;
                    this.dataTitles = response.data.dataTitles;
                    this.others = response.data.others;
                    this.pagination = response.data.pagination;
                    this.spinnerData.loading = false;
                });
            }, 500),
            getFilters() {
                this.spinnerData.loading = true;
                Promise.all([axios.get(`/${this.prefixProp}/filter`).then(response => {
                    this.spinnerData.loading = false;
                    this.mainFilters = response.data.main.map(function(item, key) {
                        let data = item;
                        data.options = Object.keys(item.options).map(function(key, index) {
                            return { value: key, text: item.options[key] };
                        });
                        return data;
                    });
                    console.log(response.data.second);
                    this.secondFilters = response.data.second.map(function(item, key) {
                        let data = item;
                        data.options = Object.keys(item.options).map(function(key, index) {
                            return { value: key, text: item.options[key] };
                        });
                        return data;
                    });

                })])
                .then((allResults) => {
                    console.log(123123);
                    this.setQueryParams();
                });
            }
        }
    }
</script>

<style scoped>
.thead-title {
    cursor: pointer;
}
.table-responsive {
    position: relative;
}
.v-spinner {
    width: 100%;
    height: 100%;
    text-align: center;
    position: absolute;
    background: #00000017;
}
.custom-link {
    padding: 5px 0px;
    display: inline-block;
    text-decoration: underline;
}
.custom-text {
    /* text-align: center; */
    display: block;
    padding: 5px 0;
}

.vs__search::placeholder,
  .vs__dropdown-toggle,
  .vs__dropdown-menu {
    background: #dfe5fb;
    border: none;
    color: #afb7e4;
    text-transform: lowercase;
    font-variant: small-caps;
  }

  .vs__clear,
  .vs__open-indicator {
    fill: #394066;
  }
.tdhidden {
    display: none;
}
.page-link {
    cursor: pointer;
}

.table .custom-link {
    font-size: 15px;
}

.table .custom-text {
    font-size: 15px;
}

.status-tries {
    background: #91c7ff;
}
.status-waiting {
    background: #f5fd82db;
}
.status-paid {
    background: #38c1727a;
}
.status-refused {
    background: #ff33337a;
}
.status {
    padding: 2px 6px;
}
</style>