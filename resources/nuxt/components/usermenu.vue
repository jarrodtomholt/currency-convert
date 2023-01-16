<template>
        <div class="relative ml-3">
            <div class="inline-flex items-center space-x-2">
                <span class="text-sm">{{ username }}</span>
                <button @click="open = !open" type="button"
                         class="flex max-w-xs items-center rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                    <span class="sr-only">Open user menu</span>
                    <img class="h-8 w-8 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
                </button>
            </div>

            <transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
            >
                <div v-show="open"
                    class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                    <button @click="logout"
                            class="block w-full px-4 py-2 text-sm text-gray-700 text-left hover:bg-gray-100"
                            role="menuitem"
                            tabindex="-1"
                       id="user-menu-item-1">Sign out</button>
                </div>
            </transition>
        </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
    computed: {
    ...mapGetters({
            username: 'auth/username'
        }),
    },
    data() {
        return {
            open: false,
        }
    },
    methods: {
        logout() {
            this.$axios.delete('logout').then(() => {
                return this.$store.dispatch('auth/destroy')
            }).then(() => {
                return this.$router.replace('/login')
            })
        }
    }
}
</script>
