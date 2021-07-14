<template>
  <splide :options="options">
		<splide-slide v-for="(product, productIndex) in products" :key="productIndex">
            <div class="slide-block">    
                <h1 style="font-size: 24px; color: #000000bd;">{{ product.title }}</h1>
                <div class="circular">
                    <img :src="product.image" alt="" >
                </div>
                <div style="font-size: 13px; color: #48403e; margin-bottom: 15px" v-html="product.description"></div>
                <a class="btn btn-success" :href="'https://wa.me/' + product.phone">
                    Написать оператору в Whatsapp <i class="fab fa-whatsapp"></i>
                </a>
            </div> 
		</splide-slide>
  </splide>
</template>

<script>
import { Splide, SplideSlide } from '@splidejs/vue-splide';

export default {
    components: {
        Splide,
        SplideSlide,
    },
    props: [
        'productsProp',
    ],
    data() {
        return {
            products: this.productsProp,
            options: {
                rewind : true,
                perPage: 1,
                interval: 4000,
                pagination: false,
                autoplay: true,
            },
        };
    },
}
</script>

<style scoped>
.circular {
    display: inline-block;
    position: relative;
    width: 200px;
    height: 200px;
    overflow: hidden;
    border-radius: 50%;
    border: 5px solid #ff5d492e;
}

.circular img {
    width: 100%;
    height: 100%;
}

.slide-block {
    text-align: center;
    padding: 0px 15px;
    margin-top: 20px;
}
</style>