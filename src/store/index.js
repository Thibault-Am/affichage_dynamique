import { createStore } from 'vuex'
import VuexPersistence from 'vuex-persist'

const vuexLocal = new VuexPersistence({
    storage: window.localStorage
})

export default createStore({
    plugins: [new VuexPersistence().plugin],
    state: {
        token: "",
    },
    mutations: {
        setToken(state, token) {
            state.token = token;
        },
    },
    actions: {},
    modules: {}
})