<template>
    <div class="container">
        <div class="row justify-content-center" v-if="$gate.isAdmin()">
            <div class="col-md-12 mt-4">
                <passport-clients></passport-clients>
                <passport-authorized-clients></passport-authorized-clients>
                <passport-personal-access-tokens></passport-personal-access-tokens>
            </div>
        </div>
        <div class="row justify-content-center" v-if="!$gate.isAdmin()">
            <not-found></not-found>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Developer"
    }
</script>

<style scoped>

</style>
