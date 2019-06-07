<template>
    <footer class="main-footer d-flex p-2 px-3 bg-white border-top">
        <div :class="[contained ? 'container' : 'container-fluid']">
            <div class="row">
        <span class="copyright ml-auto my-auto mr-2">
            Powered by free & open source software created by <a href="https://github.com/riotkit-org" target="_blank">Riotkit Collective</a>. Layout by DesignRevision
        </span>
            </div>
        </div>
    </footer>
</template>
<script>

    export default {
        name: 'main-footer',
        props: {
            /**
             * Whether the footer should be wrapped in a container, or not.
             */
            contained: {
                type: Boolean,
                default: false
            },
        },
    };
</script>
