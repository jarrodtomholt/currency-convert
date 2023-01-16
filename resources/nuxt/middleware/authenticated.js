export default function ({ store, redirect }) {
    if (store.getters['auth/isAuthenticated'] === false) {
        return redirect('/login')
    }
}
