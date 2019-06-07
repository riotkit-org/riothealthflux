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
                        <span v-html="item.node.name"></span>
                    </span>

                    <span class="ml-auto text-right text-semibold text-reagent-gray">
                        {{ item.count }}x
                    </span>
                </d-list-group-item>
            </d-list-group>
        </d-card-body>
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
