<template>
    <d-card class="card-small">
        <d-card-header class="border-bottom">
            <h6 class="m-0">{{ title }}</h6>
            <div class="block-handle"></div>
        </d-card-header>

        <d-card-body class="p-0">
            <d-list-group flush class="list-group-small">
                <d-list-group-item v-for="(item, idx) in dataset" :key="idx" class="d-flex px-3">
                    <span class="text-semibold text-fiord-blue">
                        <span class="text-danger" v-if="!item.status"><i class="material-icons">clear</i></span>
                        <span class="text-success" v-if="item.status"><i class="material-icons">check</i></span>
                        &nbsp;
                        <a :href="item.url" target="_blank" class="statusLink" v-if="item.url">
                            <span v-html="item.name"></span>
                        </a>
                        <span v-html="item.name" v-if="!item.url"></span>
                    </span>
                </d-list-group-item>
            </d-list-group>
        </d-card-body>

        <d-card-footer class="border-top">
            <d-row>
                <d-col class="text-right view-report">
                    <a href="#">Total {{ dataset.length }} positions</a>
                </d-col>
            </d-row>
        </d-card-footer>
    </d-card>
</template>

<script>
    const defaultDataset = [];

    export default {
        name: 'ao-top-referrals',
        props: {
            title: {
                type: String,
                default: 'Current status',
            },
            dataset: {
                type: Array,
                default() {
                    return defaultDataset;
                },
            },
        },
    };
</script>

<style>
    .statusLink {
        color: black;
    }
</style>