<template>
    <div class="container">
        <div class="row">
            <ul>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
            </ul>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'loader'
    };
</script>

<style>
    ul{
        position: absolute;
        top:50%;
        left: 50%;
        transform: translate(-50%,-50%);
        margin: 0px;
        padding: 0px;
        display: flex;
    }
    ul li{
        list-style: none;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #fff;
        animation: animate 1.6s ease-in-out infinite;
        margin: 0px;
        padding: 0;
    }
    @keyframes animate{
        0%, 40%, 100%
        {
            transform: scale(0.2);
        }
        20%
        {
            transform: scale(1);
        }
    }
    ul li:nth-child(1){
        animation-delay:-1.4s;
        background-color: black;
        box-shadow: 0 0 50px black;
    }
    ul li:nth-child(2){
        animation-delay:-1.2s;
        background-color: black;
        box-shadow: 0 0 50px black;
    }
    ul li:nth-child(3){
        animation-delay:-1s;
        background-color: black;
        box-shadow: 0 0 50px black;
    }
    ul li:nth-child(4){
        animation-delay:-0.8s;
        background-color: black;
        box-shadow: 0 0 50px black;
    }
    ul li:nth-child(5){
        animation-delay:-0.6s;
        background-color: red;
        box-shadow: 0 0 50px red;
    }
    ul li:nth-child(6){
        animation-delay:-0.4s;
        background-color: red;
        box-shadow: 0 0 50px red;
    }
    ul li:nth-child(7){
        animation-delay:-0.2s;
        background-color: red;
        box-shadow: 0 0 50px red;
    }

    ul li:nth-child(8){
        animation-delay:-0.2s;
        background-color: red;
        box-shadow: 0 0 50px red;
    }
</style>