<template>
    <div class="min-h-full">
        <Navbar></Navbar>

        <div class="py-10">
            <main>
                <Nuxt />
            </main>
        </div>
    </div>
</template>

<script>
import Navbar from '@/components/navbar'

export default {
    middleware: 'authenticated',
    components: {
        Navbar,
    },
}
</script>
