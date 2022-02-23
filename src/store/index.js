import { createStore } from 'vuex'
import VuexPersistence from 'vuex-persist'

const vuexLocal = new VuexPersistence({
    storage: window.localStorage
})

export default createStore({
    plugins: [new VuexPersistence().plugin],
    state: {
        token: "42836be2-8d69-4e3e-9a69-5fe9bc39aa5c",
    },
    mutations: {
        setToken(state, token) {
            state.token = token;
        },
    },
    actions: {},
    modules: {}
})