<template>
    <div>
        <div class="row">
            <div class="col-11">
                <div class="card mb-2" id="filter">
                    <div class="card-header py-1">
                        Фильтр
                        <small class="float-right">
                            <a href="#" id="filter-toggle" class="btn btn-default btn-sm" title="Скрыть/показать">
                                <i class="fa fa-toggle-off"></i>
                            </a>
                        </small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-4" v-for="(filter, filterIndex) in filters" :key="filterIndex">
                                <div class="form-group">
                                    <label for="subscriptionStatus">{{ filter.title }}</label>
                                    <div v-if="filter.type == 'select'">
                                        <select class="form-control" :id="filter.name" :name="filter.name" multiple v-model="queryParams[filter.name]">
                                            <option v-for="(option, optionIndex) in filter.options" :key="optionIndex" :value="optionIndex">{{ option }}</option>
                                        </select>
                                    </div>
                                    <div v-if="filter.type == 'input'">
                                        <input class="form-control" type="text" :name="filter.name" :value="queryParams[filter.name]" @change="changeFilterValue($event, filter.name)">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary" @click="applyFilter()">Применить фильтр</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive bg-white">
            <pulse-loader class="spinner" :loading="spinnerData.loading" :color="spinnerData.color" :size="spinnerData.size"></pulse-loader>
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th scope="col" v-for="(item, dataTitlesIndex) in dataTitles" :key="dataTitlesIndex">
                            <a class="thead-title" @click="changeSortQueryParams(item.key)">
                                {{ item.title }}
                            </a>
                        </th>
                        <th scope="col">
                            <i class="fa fa-cog"></i>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(items, itemsIndex) in data" :key="itemsIndex" :data-id="itemsIndex">
                        <td v-for="(item, name) in items" :key="name" :class="{ editable: item.type }">
                            <div v-if="item.type == 'select'">
                                <select :name="name" v-model="item.value" class="form-control form-control-sm">
                                    <option v-for="(collection, collectionIndex) in others[item.collection]" :key="collectionIndex" :value="collectionIndex">
                                        {{ collection }}
                                    </option>
                                </select>
                            </div>
                            <div v-else-if="item.type == 'input'">
                                <input type="text" class="form-control form-control-sm" v-model="item.value" />
                            </div>
                            <div v-else>
                                {{ item.value }}
                            </div>
                        </td>
                        <td class="text-right">
                            <button @click="saveItem(items, items.id.value)" type="button" class="btn btn-danger btn-sm save-button" title="Сохранить">
                                <i class="fa fa-save"></i>
                            </button>
                            <div class="dropdown btn-group" role="group">
                                <button class="btn btn-outline-dark btn-sm dropdown-toggle" type="button" :id="'dropdownMenuButton' + itemsIndex" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-cog"></i>
                                </button>
                                <div class="dropdown-menu" :aria-labelledby="'dropdownMenuButton' + itemsIndex">
                                    <a :href="'/'+ prefix +'/'+ items.id.value +'/edit'" class="dropdown-item" title="Редактировать">
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
    </div>
</template>

<script>
    export default {
        props: ['prefixProp'],
        data() {
            return {
                prefix: this.prefixProp,
                filters: [],
                data: [],
                dataTitles: [],
                others: [],
                pagination: [],
                queryParams: [],
                spinnerData: {
                    loading: false,
                    color: '#6cb2eb',
                    size: '100px',
                },
            }
        },
        mounted() {
            this.setQueryParams();
            this.getData();
            this.getFilters();
        },
        methods: {
            saveItem(items, id) {
                let data = {};
                Object.keys(items).forEach(function(name) {
                    data[name] = items[name].value;
                });
                this.spinnerData.loading = true;
                axios.post(`/api/${this.prefix}/${id}`, data)
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
            changeFilterValue(e, filterName) {
                console.log(e.target.value);
                this.queryParams[filterName] = e.target.value;
            },
            setAddressBar() {
                if (Object.keys(this.queryParams).length > 0) {
                    let strQueryParams = new URLSearchParams(this.queryParams).toString()
                    history.pushState('', '', '?' + strQueryParams);
                }
            },
            setQueryParams() {
                const params = new URLSearchParams(window.location.search);

                const paramsObj = Array.from(params.keys()).reduce(
                    (acc, val) => ({ ...acc, [val]: params.get(val) }),
                    {}
                );
                this.queryParams = paramsObj;
            },
            getData() {
                this.spinnerData.loading = true;
                axios.get(`/api/${this.prefixProp}`, { params: this.queryParams }).then(response => {
                    this.data = response.data.data;
                    this.dataTitles = response.data.dataTitles;
                    this.others = response.data.others;
                    this.pagination = response.data.pagination;
                    this.spinnerData.loading = false;
                });
            },
            getFilters() {
                this.spinnerData.loading = true;
                axios.get(`/api/${this.prefixProp}/filter`).then(response => {
                    this.filters = response.data;
                    this.spinnerData.loading = false;
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
</style>