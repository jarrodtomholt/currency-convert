<template>
    <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 mb-6 text-center text-3xl font-bold tracking-tight text-gray-900">
                Register an account
            </h2>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <form @submit.prevent="register" class="space-y-6" method="POST">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Name</label>
                        <div class="mt-1">
                            <input v-model="name" id="name" name="name" type="text" autocomplete="name" required
                                   class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                        <div class="mt-1">
                            <input v-model="email" id="email" name="email" type="email" autocomplete="email" required
                                   class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <div class="mt-1">
                            <input v-model="password" id="password" name="password" type="password"
                                   autocomplete="current-password" required class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                            Confirm Password
                        </label>
                        <div class="mt-1">
                            <input v-model="password_confirmation" id="password_confirmation" name="password_confirmation" type="password"
                                   autocomplete="current-password" required class="block w-full appearance-none rounded-md border border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>


                    <div class="pb-6">
                        <button type="submit" class="flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Sign in</button>
                    </div>

                    <nuxt-link class="block text-sm font-medium text-indigo-600 hover:text-indigo-500" to="login">
                        &lt; Back to Login
                    </nuxt-link>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    layout: 'unauthenticated',
    data() {
        return {
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
            submitting: false,
        }
    },
    methods: {
        register() {
            if (this.submitting) {
                return
            }

            this.submitting = true

            this.$axios.post('register', {
                name: this.name,
                email: this.email,
                password: this.password,
                password_confirmation: this.password_confirmation,
            }).then((response) => {
                return this.$store.dispatch('auth/store', response.data)
            }).then(() => {
                return this.$router.replace('/')
            }).finally(() => {
                this.submitting = false
            })
        }
    },
}
</script>
