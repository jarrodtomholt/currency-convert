<template>
    <div class="flex min-h-screen flex-col justify-center py-12 sm:px-6 lg:px-8">
        <Nuxt />
    </div>
</template>

<script>
export default {
    middleware: 'guest',
    name: 'UnauthenticatedLayout',
}
</script>
