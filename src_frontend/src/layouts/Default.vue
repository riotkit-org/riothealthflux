<template>
    <d-container fluid>
        <d-row>
            <d-col class="main-content offset-lg-1 offset-md-3 p-0" tag="main" lg="10" md="9" sm="12">
                <slot/>
                <main-footer/>
            </d-col>

        </d-row>
    </d-container>
</template>

<script>
    import MainFooter from '@/components/layout/MainFooter/MainFooter.vue';

    export default {
        name: 'riotkit-dashboard',
        components: {
            MainFooter,
        }
    };
</script>

