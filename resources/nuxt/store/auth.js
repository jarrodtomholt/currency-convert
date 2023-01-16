export const state = () => ({
    accessToken: JSON.parse(localStorage.getItem('five9.currency'))?.token ?? null,
    name: JSON.parse(localStorage.getItem('five9.currency'))?.name ?? null,
})

export const getters = {
    isAuthenticated: (state) => {
        return state.accessToken !== null
    },
    accessToken: (state) => {
        return state.accessToken
    },
    username: (state) => {
        return state.name
    },
}

export const mutations = {
    STORE_AUTH_DETAILS(state, payload) {
        state.accessToken = payload.token
        state.name = payload.name

        localStorage.setItem('five9.currency', JSON.stringify({
            token: state.accessToken,
            name: state.name,
        }))
    },
    DESTROY_AUTH_DETAILS(state) {
        state.accessToken = null
        state.name = null

        localStorage.removeItem('five9.currency')
    }
}

export const actions = {
    store({ commit }, response) {
        commit('STORE_AUTH_DETAILS', response)
    },
    destroy({ commit }) {
        commit('DESTROY_AUTH_DETAILS')
    }
}
