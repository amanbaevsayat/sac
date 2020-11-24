<template>
    <div>
        <a @click="$bvModal.show('modal-customer-create')" class="btn btn-info btn-block text-white mb-2" title="Добавить">
            Добавить клиента
        </a>
        <customer-component type-prop="create"></customer-component>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
