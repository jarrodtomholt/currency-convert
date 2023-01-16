export const state = () => ({
    currencies: null,
})

export const getters = {
    currencies: (state) => {
        return state.currencies
    },
}

export const mutations = {
    STORE_CURRENCIES(state, payload) {
        state.currencies = payload
    },
}

export const actions = {
    store({ commit }, response) {
        commit('STORE_CURRENCIES', response)
    }
}
