export default function ({ $axios, store, redirect }, inject) {
    const axios = $axios.create({
        timeout: 30000,
        headers: {
            common: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        },
    })

    axios.interceptors.request.use((config) => {
        const token = store.getters['auth/accessToken']

        if (token) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        }

        return config
    })

    axios.interceptors.response.use((response) => {
        return response
    }, (error) => {
        const statusCode = error.response.status

        if (statusCode === 401) {
            console.warn('here')
            return store.dispatch('auth/destroy').then(() => {
                return redirect('/login')
            })
        }

        if ([400, 403, 422].includes(statusCode) && error.response.data.message) {
            alert(error.response.data.message) // use a fancy library or something here?
        }

        return Promise.resolve({ error })
    })

    inject('axios', axios)
}
